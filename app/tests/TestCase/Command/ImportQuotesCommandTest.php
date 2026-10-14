<?php
declare(strict_types=1);

namespace App\Test\TestCase\Command;

use App\Command\ImportQuotesCommand;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Cake\Http\TestSuite\HttpClientTrait;
use Cake\TestSuite\TestCase;
use RuntimeException;

class ImportQuotesCommandTest extends TestCase
{
    use HttpClientTrait;

    protected array $fixtures = ['app.Cars', 'app.Quotes'];

    protected function setUp(): void
    {
        parent::setUp();
        Configure::write('Dinggo', [
            'baseUrl' => 'https://example.test/phptest',
            'username' => 'test-user',
            'key' => 'test-key',
        ]);
    }

    public function testRepeatedQuoteImportReplacesPreviousQuotes(): void
    {
        $quotes = $this->getTableLocator()->get('Quotes');
        for ($run = 0; $run < 2; $run++) {
            $this->assertSame(0, $this->runImport());
        }

        $saved = $quotes->find()->orderBy(['repairer' => 'ASC'])->all();
        $this->assertSame(['First repairer', 'Second repairer'], $saved->extract('repairer')->toList());
        $this->assertSame([1, 1], $saved->extract('car_id')->toList());
    }

    public function testApiFailurePreservesPreviousQuotes(): void
    {
        $quotes = $this->getTableLocator()->get('Quotes');
        $before = $quotes->find()->all()->toArray();
        $this->assertSame(1, $this->runImport(500));
        $this->assertEquals($before, $quotes->find()->all()->toArray());
    }

    public function testFailedSaveRollsBackQuoteReplacement(): void
    {
        $quotes = $this->getTableLocator()->get('Quotes');
        $before = $quotes->find()->all()->toArray();
        $attempts = 0;
        $listener = function () use (&$attempts): void {
            $attempts++;
            if ($attempts === 2) {
                throw new RuntimeException('Simulated save failure');
            }
        };
        $quotes->getEventManager()->on('Model.beforeSave', $listener);

        try {
            $this->assertSame(1, $this->runImport());
        } finally {
            $quotes->getEventManager()->off('Model.beforeSave', $listener);
        }

        $this->assertSame(2, $attempts);
        $this->assertEquals($before, $quotes->find()->all()->toArray());
    }

    private function runImport(int $status = 200): int
    {
        $this->mockClientPost(
            'https://example.test/phptest/quotes',
            $this->newClientResponse($status, [], json_encode([
                'success' => 'ok', 'quotes' => [
                    ['price' => '150.00', 'repairer' => 'First repairer', 'overview_of_work' => 'Repair bumper'],
                    ['price' => '250.00', 'repairer' => 'Second repairer', 'overview_of_work' => 'Paint bumper'],
                ],
            ], JSON_THROW_ON_ERROR)),
        );

        return (new ImportQuotesCommand())->execute(
            new Arguments([], [], []),
            $this->createStub(ConsoleIo::class),
        );
    }
}
