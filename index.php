<?php

function createArray($num = 100){
        $arr = [];
        $count = 1;
        while($count <= $num){
                $arr[] = $count;
                $count++;
        }
        return $arr;
}
function arrayRand($type, $arr, $num){
        switch($type){
            case 'array_rand':
                return array_rand($arr, $num);
                break;
            case 'fisher':
                return shuffle_fisher_yates($arr, $num);
                break;
        }
}
function iterate($arr, $iterations, $collectionLength, $randomizeTechnique){
        $collections = [];
        for($i=0;$i<$iterations;$i++){
                $collections[] = arrayRand($randomizeTechnique, $arr, $collectionLength);
        }
        return $collections;
}
function flatten($collections){
        $flatArr = [];
        foreach($collections as $collection){
                $flatArr = array_merge($flatArr, $collection);
        }
        return $flatArr;
}
function groupArray($arr){
        $groupedArr = [];
        foreach($arr as $item){
                if(!array_key_exists($item, $groupedArr)){
                        $groupedArr[$item] = [];
                }
                $groupedArr[$item][] = $item+1;
        }
        ksort($groupedArr);
        return ($groupedArr);
}
function shuffle_fisher_yates($items, $num) {
    $pickedItems = [];
    for($i=count($items)-1;$i>0;$i--){
        $j = @mt_rand(0, $i);
        $tmp = $items[$i];
        $items[$i] = $items[$j];
        $items[$j] = $tmp;
    }

    for($i=0;$i<$num;$i++){
        $pickedItems[] = $items[$i];
    }
    return $pickedItems;
}

class Results
{
    public function __construct($numItems, $pickedItems, $iterations)
    {
        $this->numItems = $numItems;
        $this->pickedItems = $pickedItems;
        $this->iterations = $iterations;
    }
    public function barWidth($group)
    {
        return (100 / $this->numItems);
    }
    public function barHeight($group)
    {
        return (count($group) / $this->iterations) * 100;
    }
    public function getMean($groupedArr){
        $averages = [];
        foreach($groupedArr as $group){
            $averages[] = $this->barHeight($group);
        }
        $average = array_sum($averages) / count($averages);
        return $average;
    }
}

if(isset($_POST['num_items']) && isset($_POST['picked_items']) && isset($_POST['iterations'])) {
    $numItems = $_POST['num_items'];
    $pickedItems = $_POST['picked_items'];
    $iterations = $_POST['iterations'];

    $arr = createArray($numItems);
    $collections = iterate($arr,$iterations,$pickedItems, 'array_rand');
    $collections2 = iterate($arr,$iterations,$pickedItems, 'fisher');
    $grouped = groupArray(flatten($collections));
    $grouped2 = groupArray(flatten($collections2));


    $result = new Results($numItems, $pickedItems, $iterations);
}

?>
<!DOCTYPE html>
<html>
<head>

    <style type="text/css">
        .container {
            height: 200px;
            border: 1px solid red;
            position: relative;
        }

        .bar {
            float: left;
            background: blue;
            box-sizing: border-box;
            border: 1px solid white;
            position: relative;
            z-index: 1;
        }

        .average {
            height: 16%;
            border-bottom: 1px solid black;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 2;
            opacity: .4;
        }
        .average label{
            position: absolute;
            top:100%;
        }
    </style>
</head>
<body>
<form action="" method="POST">
    <div class="form-group">
        <label>Number of Items</label>
        <input type="number" name="num_items" <?php if(isset($_POST['num_items'])){ echo 'value="'.$_POST['num_items'].'"'; } ?> />
    </div>
    <div class="form-group">
        <label>Number of Picked Items</label>
        <input type="number" name="picked_items" <?php if(isset($_POST['picked_items'])){ echo 'value="'.$_POST['picked_items'].'"'; } ?> />
    </div>
    <div class="form-group">
        <label>Iterations</label>
        <input type="number" name="iterations" <?php if(isset($_POST['iterations'])){ echo 'value="'.$_POST['iterations'].'"'; } ?> />
    </div>
<!--     <div class="form-group">
        <label>Randomize Technique</label>
        <select name="randomize_technique">
            <option value="array_rand" <?php if(isset($_POST['array_rand'])){ echo 'selected="selected"'; } ?>>array_rand()</option>
            <option value="fisher" <?php if(isset($_POST['randomize_technique'])){ echo 'selected="selected"'; } ?>>Fisher Yates</option>
        </select>
    </div> -->
    <input type="submit" value="Calculate" />
</form>

<h3>Resutls:</h3>
<h2>PHP's array_rand() Function</h2>
<div class="container">
<?php
    if(isset($_POST['num_items']) && isset($_POST['picked_items']) && isset($_POST['iterations'])){
        foreach($grouped as $group){
?>
            <div class="bar"
                 style="width:<?php echo $result->barWidth($group);  ?>%; height:<?php echo $result->barHeight($group); ?>%;">
                <label>Item#: <?php echo $group[0]; ?> (<?php echo $result->barHeight($group); ?>%, Picked <?php echo count($group); ?> times)</label>
            </div>
<?php
        }
?>
    <div class="average" style="height:<?php echo $result->getMean($grouped); ?>%;"><label>Mean:  <?php echo $result->getMean($grouped); ?>%</label></div>
<?php
    }else{
        echo '<p>Submit the form to see results</p>';
    }
?>
</div>
<div style="z-index:2;background:rgba(255,255,255,.7);width:100%;height:100px;">100%</div>

<h2>Fisher Yates Shuffle</h2>
<div class="container" style="margin-top:100px;">
<?php
    if(isset($_POST['num_items']) && isset($_POST['picked_items']) && isset($_POST['iterations'])){
        foreach($grouped2 as $group){
?>
            <div class="bar"
                 style="width:<?php echo $result->barWidth($group);  ?>%; height:<?php echo $result->barHeight($group); ?>%; background:purple;">
                <label>Item#: <?php echo $group[0]; ?> (<?php echo $result->barHeight($group); ?>%)</label>
            </div>
<?php
        }
?>
    <div class="average" style="height:<?php echo $result->getMean($grouped2); ?>%;"><label>Mean:  <?php echo $result->getMean($grouped2); ?>%</label></div>
<?php
    }else{
        echo '<p>Submit the form to see results</p>';
    }
?>
</div>
<div style="position:absolute;left:20px;z-index:2;background:rgba(255,255,255,.7);width:100%;height:400px;">100%</div>
</body>
</html>