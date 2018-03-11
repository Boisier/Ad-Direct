<nav class="breadcrumb bg-white py-0">
  <a class="breadcrumb-item" href="#" onclick="event.preventDefault(); DLM.go('/home/params/')"><?php echo \__("adminMenu-Params"); ?></a>
  <span class="breadcrumb-item active"><?php echo \__("globalParameters"); ?></span>
</nav>
<div class="list-group">
	<?php
	foreach($this->params as $param)
	{
		/*
		[Param]
			name
			value
			type
			lastEdit
		*/
		?>
		<a href="#" class="list-group-item list-group-item-action" onclick="event.preventDefault(); DLM.go('/param/form/editGlobal/<?php echo $param['name']; ?>')">
			<h5 class="list-group-item-heading mb-0">
				<?php echo \__($param['name']) ?>
			</h5>
			<small>
				<?php 
				switch($param['type'])
				{
					case "list":
						
						echo str_replace(",", ", ", $param['value']);
						
					break;
					case "duration":
						
						$paramValue = explode(",", $param['value']);
						
						$durations = [];
						
						if($paramValue[0] != 0)
						{
							if($paramValue[0] == 1)
								array_push($durations, "1 ".\__("year")." ");
							else
								array_push($durations, $paramValue[0]." ".\__("years")." ");
						}
						
						if($paramValue[1] != 0)
						{
							if($paramValue[1] == 1)
								array_push($durations, "1 ".\__("month")." ");
							else
								array_push($durations, $paramValue[1]." ".\__("months")." ");
						}
						
						if($paramValue[2] != 0)
						{
							if($paramValue[2] == 1)
								array_push($durations, "1 ".\__("week")." ");
							else
								array_push($durations, $paramValue[2]." ".\__("weeks")." ");
						}
						
						if($paramValue[3] != 0)
						{
							if($paramValue[3] == 1)
								array_push($durations, "1 ".\__("day")." ");
							else
								array_push($durations, $paramValue[3]." ".\__("days")." ");
						}
						
						if($paramValue[4] != 0)
						{
							if($paramValue[4] == 1)
								array_push($durations, "1 ".\__("hour")." ");
							else
								array_push($durations, $paramValue[4]." ".\__("hours")." ");
						}
						
						if($paramValue[5] != 0)
						{
							if($paramValue[5] == 1)
								array_push($durations, "1 ".\__("minute")." ");
							else
								array_push($durations, $paramValue[5]." ".\__("minutes")." ");
						}
						
						if($paramValue[6] != 0)
						{
							if($paramValue[6] == 1)
								array_push($durations, "1 ".\__("second")." ");
							else
								array_push($durations, $paramValue[6]." ".\__("seconds")." ");
						}
						
						echo implode(", ", $durations);
						
					break;
					default;
					    
					    $extract = strip_tags(str_replace('</p>', " ", str_replace('<br />', " ", nl2br($param['value']))));
					    
					    
					    if(strlen($extract) > 99)
						    $extract = substr($extract, 0, 96) . "...";
					    
					    echo $extract;
				}
				?>
			</small>
		</a>
		<?php
	}
	?>
</div>