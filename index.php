<?php
// SHOW ERRORS FOR DEV
error_reporting(E_ALL & ~E_NOTICE);
ini_set('log_errors', 1);
ini_set('error_log', 'errors.log');

// load app
require "app/KitchenManager.php";
$km = new KitchenManager();

if($_GET['itemedit']){
    $item_info = $km->selectOne('items', array('item_id' => $_GET['itemedit']));
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    switch($_POST['form_type']){
        case 'item':
            $km->item_add($_POST['brand'], $_POST['name'], intval($_POST['stock']), intval($_POST['restock']), intval($_POST['category_id']), intval($_POST['location_id']));
            break;
        case 'item_edit':
            $km->item_edit($_POST['brand'], $_POST['name'], intval($_POST['stock']), intval($_POST['restock']), intval($_POST['category_id']), intval($_POST['location_id']), intval($_POST['item_id']));
            break;
        case 'category':
            $km->category_add($_POST['name']);
            break;
        case 'location':
            $km->location_add($_POST['code'], $_POST['desc']);
            break;
    }

    header('location: index.php');
    exit;
}

// FOR NOW... UGLINESS
?>
<!DOCTYPE html>
<html>
<head>
    <title>wow, yummy pantry</title>
    <style>
        .cols{
            display:grid;
            grid-template-columns:1fr 1fr 1fr;
        }
        @media(max-width:1000px){
            grid-template-columns:1fr;
        }
        table{
            margin-top:30px;
        }
    </style>
</head>
<body>
<h1>KITCHEN MANAGER. YUM.</h1>
<hr>
<div class="cols">
<section class="items">
    <h2><?php echo $_GET['itemedit'] ? 'EDIT ITEM' : "ITEMS"; ?></h2>
    <form method="post" action="">
        <input type="hidden" name="form_type" value="<?php echo $_GET['itemedit'] ? 'item_edit' : 'item'; ?>">
        <?php if($_GET['itemedit']){ ?><input type="hidden" name="item_id" value="<?php echo $item_info['item_id']; ?>"><?php } ?>
        brand. <input type="text" name="brand" placeholder="Brand" value="<?php echo $item_info['item_brand']; ?>"><br>
        name. <input type="text" name="name" placeholder="Name" value="<?php echo $item_info['item_name']; ?>"><br>
        qty. <input type="number" name="stock" step="1" value="<?php echo $item_info['item_stock'] ?: 1; ?>"><br>
        restock? <input type="checkbox" name="restock" value="1" <?php echo $item_info['item_restock'] ? 'checked' : ''; ?>><br>
        category: <select name="category_id">
            <option value="0">Select</option>
            <?php foreach($km->select('categories') as $cat){
                $sel = $cat['category_id'] == $item_info['category_id'] ? 'selected' : '';
                echo "<option ".$sel." value='".$cat['category_id']."'>".$cat['category_name'].'</option>';
            } ?>
        </select><br>
        location: <select name="location_id">
            <option value="0">Select</option>
            <?php foreach($km->select('locations') as $loc){
                $sel = $loc['location_id'] == $item_info['location_id'] ? 'selected' : '';
                echo "<option ".$sel." value='".$loc['location_id']."'>".$loc['location_desc'].'</option>';
            } ?>
        </select><br>
        <input type="submit" value="Save Item">
    </form>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>BRAND</th>
            <th>NAME</th>
            <th>QTY</th>
            <th>CATEGORY</th>
            <th>LOCATION</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach($km->query('SELECT * FROM items JOIN categories USING (category_id) LEFT JOIN locations USING (location_id)') as $i){
            echo '<tr>';
            echo '<td><a href="?itemedit='.$i['item_id'].'">'.$i['item_id'].'</a></td>';
            echo '<td>'.$i['item_brand'].'</td>';
            echo '<td>'.$i['item_name'].'</td>';
            echo '<td>'.$i['item_stock'].'</td>';
            echo '<td>'.$i['category_name'].'</td>';
            echo '<td>'.$i['location_code'].'</td>';
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>
</section>
<section class="categories">
    <h2>CATEGORIES</h2>
    <form method="post" action="">
        <input type="hidden" name="form_type" value="<?php echo $_GET['catedit'] ? 'cat_edit' : 'category'; ?>">
        name. <input type="text" name="name" placeholder="Name"><br>
        <input type="submit" value="Save Category">
    </form>
    <table>
        <thead>
        <tr>
            <th>NAME</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach($km->select('categories') as $cat){
            echo '<tr>';
            echo '<td>'.$cat['category_name'].'</td>';
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>
</section>
<section class="locations">
    <h2>LOCATIONS</h2>
    <form method="post" action="">
        <input type="hidden" name="form_type" value="<?php echo $_GET['locedit'] ? 'loc_edit' : 'location'; ?>">
        code. <input type="text" name="code" placeholder="Code"><br>
        description. <input type="text" name="desc" placeholder="Description"><br>
        <input type="submit" value="Save Location">
    </form>
    <table>
        <thead>
            <tr>
                <th>CODE</th>
                <th>DESCRIPTION</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($km->select('locations') as $loc){
                    echo '<tr>';
                    echo '<td>'.$loc['location_code'].'</td>';
                    echo '<td>'.$loc['location_desc'].'</td>';
                    echo '</tr>';
                }
            ?>
        </tbody>
    </table>
</section>
</div>

</body>
</html>
