<nav class="navbar navbar-expand-lg navbar-light bg-light justify-content-between mb-3">
    <span class="navbar-brand"><?php echo \__("clientsLogs"); ?></span>
</nav>
<table class="table table-striped table-sm" id="logTable">
    <thead>
        <tr>
            <th scope="col" style="width: 130px;"><?php echo \__("date"); ?></th>
            <th scope="col"><?php echo \__("action"); ?></th>
            <th scope="col"><?php echo \__("status"); ?></th>
            <th scope="col"><?php echo \__("message"); ?></th>
        </tr>
    </thead>
    <tfoot>
        <tr class="bg-white">
            <td colspan="4" class="text-right">
                <div id="endOfLogs" style="display:<?php echo $this->hasMore ? 'none' : 'block'; ?>">
                    <?php echo \__("logsEnd"); ?>
                </div>
            </td>
        </tr>
    </tfoot>
    <tbody>
        <?php
        echo $this->logs;
        ?>
    </tbody>
</table>
<script type="text/javascript">
    var currentLogPage = 0;
</script>
<div class="bigParaph" id="ShowMoreLogsBtn" style="display:<?php echo  $this->hasMore ? 'block' : 'none'; ?>; margin-top: 35px;">
    <button type="button" class="btn btn-outline-secondary" onclick="getNextLogPage(<?php echo $this->userID ?>)">
        <?php echo \__("nextLogs"); ?>
    </button>
</div>