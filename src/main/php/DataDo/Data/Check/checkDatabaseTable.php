<style type="text/css" scoped>
    <?php require_once 'checkDatabaseTable.css'; ?>

</style>

<table class="check-table">
    <tr>
        <th colspan="4"><h2>DataDo Repository Check</h2></th>
    </tr>
    <tr>
        <th>Class</th>
        <td colspan="3"><?= $this->entityClass->getName() ?></td>
    </tr>
    <tr>
        <th>Table Name</th>
        <td colspan="3"><?= $this->tableName ?></td>
    </tr>
    <tr>
        <th>Driver</th>
        <td colspan="3"><?= $pdoAtt(PDO::ATTR_DRIVER_NAME) ?></td>
    </tr>
    <tr>
        <th>Connection</th>
        <td colspan="3"><?= $pdoAtt(PDO::ATTR_CONNECTION_STATUS) ?></td>
    </tr>
    <tr>
        <th>Database Status</th>
        <td colspan="3"><?= $pdoAtt(PDO::ATTR_SERVER_INFO) ?></td>
    </tr>
    <tr>
        <th rowspan="<?= count($properties) + 1 ?>">Properties</th>
        <th>Class Property</th>
        <th>Expected database column</th>
        <th>Actual database column</th>
    </tr>
    <?php foreach ($properties as $prop): ?>
        <tr class="<?= $getClass($prop) ?>">
            <td>
                <?= $issetOr($prop->propertyName) ?>
            </td>
            <td>
                <?= $issetOr($prop->expectedColumnName) ?>
            </td>
            <td>
                <?= $issetOr($prop->actualColumnName) ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<script>
    <?php require_once 'checkDatabaseTable.js' ?>

</script>
