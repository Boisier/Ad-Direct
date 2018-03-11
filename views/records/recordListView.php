<?php
	$lineClass = "";

	if($this->result == \Objects\Record::REFUSED || $this->result == \Objects\Record::UNKNOWN)
		$lineClass .= "table-warning";
	else if($this->result == \Objects\Record::UNAUTHORIZED || $this->result == \Objects\Record::FATAL_ERROR)
		$lineClass .= "table-danger";
?>
<tr class="<?php echo $lineClass; ?>">
	<td><small><?php echo 	date($this->dateformat, $this->date); ?></small></td>
	<td><?php echo \__("recordType".$this->action); ?></td>
	<td><?php echo $this->resultText; ?></td>
	<td><?php echo $this->message; ?></td>
</tr>