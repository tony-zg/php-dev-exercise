<?php
$this->assign('title', 'Cars');
?>

<div class="content">
    <h1>Cars</h1>

    <?php if ($cars->isEmpty()): ?>
        <p>No cars have been imported yet.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Plate</th>
                        <th scope="col">State</th>
                        <th scope="col">Make</th>
                        <th scope="col">Model</th>
                        <th scope="col">Year</th>
                        <th scope="col">Colour</th>
                        <th scope="col">VIN</th>
                        <th scope="col">Quotes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cars as $car): ?>
                        <tr>
                            <td><?= h($car->license_plate) ?></td>
                            <td><?= h($car->license_state) ?></td>
                            <td><?= h($car->make) ?></td>
                            <td><?= h($car->model) ?></td>
                            <td><?= h($car->year) ?></td>
                            <td><?= h($car->colour) ?></td>
                            <td><?= h($car->vin) ?></td>
                            <td><?= $this->Html->link('View quotes', ['action' => 'view', $car->id]) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
