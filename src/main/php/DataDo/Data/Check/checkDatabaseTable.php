<style type="text/css" scoped>
    <?php require_once 'checkDatabaseTable.css'; ?>

</style>

<table class="check-table">
    <tr>
        <th colspan="4"><h2>DataDo Repository Check</h2></th>
    </tr>
    <tr>
        <th>Class</th>
        <td colspan="3"><?php echo $this->entityClass->getName() ?></td>
    </tr>
    <tr>
        <th>Table Name</th>
        <td colspan="3"><?php echo $this->tableName ?></td>
    </tr>
    <tr>
        <th>Driver</th>
        <td colspan="3"><?php echo $pdoAtt(PDO::ATTR_DRIVER_NAME) ?></td>
    </tr>
    <tr>
        <th>Connection</th>
        <td colspan="3"><?php echo $pdoAtt(PDO::ATTR_CONNECTION_STATUS) ?></td>
    </tr>
    <tr>
        <th>Database Status</th>
        <td colspan="3"><?php echo $pdoAtt(PDO::ATTR_SERVER_INFO) ?></td>
    </tr>
    <tr>
        <th rowspan="<?php echo count($properties) + 1 ?>">Properties</th>
        <th>Class Property</th>
        <th>Expected database column</th>
        <th>Actual database column</th>
    </tr>
    <?php foreach ($properties as $prop): ?>
        <tr class="<?php echo $getClass($prop) ?>">
            <td>
                <?php echo $issetOr($prop->propertyName) ?>
            </td>
            <td>
                <?php echo $issetOr($prop->expectedColumnName) ?>
            </td>
            <td>
                <?php echo $issetOr($prop->actualColumnName) ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if ($showAllData): ?>
        <tr>
            <th rowspan="<?php echo count($entities) + 1 + ($entitiesError ? 1 : 0) ?>">Entities (<?php echo count($entities) ?>)</th>
        </tr>
        <?php if ($entitiesError): ?>
            <tr>
                <td colspan="3">
                    <?php echo$entitiesError->getMessage() . ' Check your table mapping.'?>
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($entities as $entity): ?>
                <tr class="entity-row">
                    <td colspan="3">
                        <?php
                        ob_start();
                        var_dump($entity);
                        echo ob_get_clean();
                        ?>
                    </td>

                </tr>
            <?php endforeach; endif; ?>

    <?php endif; ?>
</table>
