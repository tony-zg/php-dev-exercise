<?php
declare(strict_types=1);

namespace App\Test\TestCase\Command;

use App\Command\ImportCarsCommand;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Cake\Http\TestSuite\HttpClientTrait;
use Cake\TestSuite\TestCase;

class ImportCarsCommandTest extends TestCase
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

    public function testRepeatedCarImportUpdatesWithoutDuplicates(): void
    {
        $cars = $this->getTableLocator()->get('Cars');
        $existing = $cars->get(1)->toArray();
        $existing['colour'] = 'Blue';
        $newCar = array_replace($existing, ['license_state' => 'VIC']);

        for ($run = 0; $run < 2; $run++) {
            $this->mockCars([$existing, $newCar]);
            $this->assertSame(0, $this->runImport(new ImportCarsCommand()));
        }

        $this->assertSame(2, $cars->find()->count());
        $this->assertSame('Blue', $cars->get(1)->colour);
        $this->assertSame(1, $cars->find()->where(['license_state' => 'NSW'])->count());
        $this->assertSame(1, $cars->find()->where(['license_state' => 'VIC'])->count());
    }

    private function mockCars(array $records): void
    {
        $this->mockClientPost(
            'https://example.test/phptest/cars',
            $this->newClientResponse(200, [], json_encode([
                'success' => 'ok', 'cars' => $records,
            ], JSON_THROW_ON_ERROR)),
        );
    }

    private function runImport(ImportCarsCommand $command): int
    {
        return $command->execute(
            new Arguments([], [], []),
            $this->createStub(ConsoleIo::class),
        );
    }
}
