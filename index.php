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
    }

    header('location: index.php');
    exit;
}

// FOR NOW... UGLINESS
?>
<!DOCTYPE html>
<html>
<head>
    <title>kitchenr</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap/FontAwesome CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/select2-bootstrap4.min.css">

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
        .primary_bg{
            background-color:#66AE42;
        }
        body{
            background-color:#C1DEB3;
        }
        footer.footer{
            padding:30px 0;
            text-align:center;
        }
    </style>
</head>
<body class="by-blue-grey-lighten-5">

<header>
    <nav class="navbar navbar-expand-md navbar-dark primary_bg">
        <div class="container pt-2 pb-2 pr-0 pl-0">
            <a class="navbar-brand d-inline-block mr-4" href="#">kitchenr</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item mr-3 dropdown">
                        <a class="nav-link active dropdown-toggle" href="index.php" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-utensils mr-2"></i>Pantry
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item" href="category.php">Categories</a>
                            <a class="dropdown-item" href="location.php">Locations</a>
                        </div>
                    </li>
                    <li class="nav-item mr-3">
                        <a class="nav-link" href="recipes.php"><i class="fa fa-book mr-2"></i>Recipes</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                            <?php echo $_SESSION[$km->app_session_prefix."_username"]; ?>lucas
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#"><i class="fa fa-user-circle mr-2"></i>Profile</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="logout.php"><i class="fa fa-sign-out-alt mr-2"></i>Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
<main role="main" class="container">

    <h1 class="mt-5 mb-3">
        Pantry
        <div class="btn-group float-right">
            <button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#pantry_modal"><i class="fa fa-plus mr-1"></i>Item</button>
        </div>
    </h1>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover" id="pantry">
                <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($km->query('SELECT * FROM items JOIN categories USING (category_id) LEFT JOIN locations USING (location_id)') as $i){
                    echo '<tr>';
                    echo '<td>'.$i['item_brand']." - ".$i['item_name'].'</td>';
                    echo '<td>'.$i['item_stock'].'</td>';
                    echo '<td>'.$i['category_name'].'</td>';
                    echo '<td>'.$i['location_code'].'</td>';
                    echo '<td><a href="?itemedit='.$i['item_id'].'"><button type="button" class="btn btn-outline-secondary btn-sm">Edit</button></a></td>';
                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

</main>

<footer class="footer">
    <div class="container text-center">
        &copy; <?php echo date('Y'); ?>. Crafted with Care by <a href="https://lucaslower.com">Lucas Lower</a>.
    </div>
</footer>

<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="//cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script type="text/javascript">

    $(document).ready(function(){
        $('#pantry').DataTable();
        $('#category').select2({placeholder: 'Select a category', theme: 'bootstrap4'});
        $('#location').select2({placeholder: 'Select a location', theme: 'bootstrap4'});

        let itemedit = <?php echo $_GET['itemedit'] ?: 0; ?>;
        if(itemedit > 0){
            $('#pantry_modal').modal('show');
        }
    });

</script>


<div class="modal" tabindex="-1" role="dialog" id="pantry_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $_GET['itemedit'] ? 'Edit' : 'Create'; ?> Pantry Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="">
            <div class="modal-body">
                    <input type="hidden" name="form_type" value="<?php echo $_GET['itemedit'] ? 'item_edit' : 'item'; ?>">
                    <?php if($_GET['itemedit']){ ?><input type="hidden" name="item_id" value="<?php echo $item_info['item_id']; ?>"><?php } ?>

                    <div class="form-group">
                        <label for="brand">Brand</label>
                        <input class="form-control" type="text" id="brand" name="brand" placeholder="Brand" value="<?php echo $item_info['item_brand']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="name">Name</label>
                        <input class="form-control" type="text" id="name" name="name" placeholder="Name" value="<?php echo $item_info['item_name']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="stock">Quantity</label>
                        <input class="form-control" type="number" id="stock" name="stock" step="1" value="<?php echo $item_info['item_stock'] ?: 1; ?>">
                    </div>

                    <div class="form-group">
                        <label for="category">Category</label>
                        <select class="form-control" name="category_id" id="category">
                            <option value=""></option>
                            <?php foreach($km->query('SELECT * FROM categories ORDER BY category_name') as $cat){
                                $sel = $cat['category_id'] == $item_info['category_id'] ? 'selected' : '';
                                echo "<option ".$sel." value='".$cat['category_id']."'>".$cat['category_name'].'</option>';
                            } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="location">Location</label>
                        <select class="form-control" name="location_id" id="location">
                            <option value=""></option>
                            <?php foreach($km->query('SELECT * FROM locations ORDER BY location_code') as $loc){
                                $sel = $loc['location_id'] == $item_info['location_id'] ? 'selected' : '';
                                echo "<option ".$sel." value='".$loc['location_id']."'>(".$loc['location_code'].") ".$loc['location_desc'].'</option>';
                            } ?>
                        </select>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="restock" name="restock" value="1" <?php echo $item_info['item_restock'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="restock">Restock?</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary"><?php echo $_GET['itemedit'] ? 'Edit' : 'Create'; ?> Item</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
