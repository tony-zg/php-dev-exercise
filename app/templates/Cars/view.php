<?php
$this->assign('title', 'Quotes for ' . $car->license_plate);
?>

<div class="content">
    <p><?= $this->Html->link('Back to cars', ['action' => 'index']) ?></p>

    <h1><?= h($car->license_plate) ?> (<?= h($car->license_state) ?>)</h1>
    <p><?= h($car->year) ?> <?= h($car->make) ?> <?= h($car->model) ?></p>
    <p>Colour: <?= h($car->colour) ?><br>VIN: <?= h($car->vin) ?></p>

    <h2>Repair quotes</h2>

    <?php if (empty($car->quotes)): ?>
        <p>No quotes have been imported for this car yet.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Repairer</th>
                        <th scope="col">Price</th>
                        <th scope="col">Overview of work</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($car->quotes as $quote): ?>
                        <tr>
                            <td><?= h($quote->repairer) ?></td>
                            <td><?= $this->Number->format($quote->price, ['places' => 2]) ?></td>
                            <td><?= h($quote->overview_of_work) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
