<?php require "../../config/config.php"; ?>
<?php require "../../libs/App.php"; ?>
<?php require "../layouts/header.php"; ?>
<?php 

$app = new App;   
$app->validateSessionAdminInside();

// Obtener el ID del food a editar
if(!isset($_GET['id'])) {
    echo "<script>window.location.href='show-foods.php'</script>";
    exit();
}

$id = $_GET['id'];

// Obtener los datos actuales del food
$query = "SELECT * FROM foods WHERE id = :id";
$query = "SELECT * FROM foods WHERE id='$id'";
$arr = [":id" => $id];
$food = $app->selectOne($query, $arr);

// Verificar si existe el food
if(!$food) {
    echo "<script>alert('Food no encontrado'); window.location.href='show-foods.php'</script>";
    exit();
}

if(isset($_POST['submit'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $meal_id = $_POST['meal_id'];
    
    // Inicializar variables para la imagen
    $image = $food->image; // Mantener la imagen actual por defecto
    $updateImage = false;
   
    // Verificar si se subió una nueva imagen
    if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $originalName = $file['name'];
        $tempPath = $file['tmp_name'];
        $fileSize = $file['size'];
        
        // Validaciones de seguridad para la nueva imagen
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        
        // Obtener extensión del archivo
        $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        
        // Validar extensión
        if(!in_array($fileExtension, $allowedExtensions)) {
            echo "<script>alert('Error: Tipo de archivo no permitido. Solo se permiten: " . implode(', ', $allowedExtensions) . "');</script>";
        }
        else if($fileSize > $maxFileSize) {
          // Validar tamaño
            echo "<script>alert('Error: El archivo es demasiado grande. Tamaño máximo: 5MB');</script>";
        }
        else {
            // Generar nombre único para la nueva imagen
            $uniqueName = uniqid() . '_' . time() . '.' . $fileExtension;
            $dir = "foods-images/" . $uniqueName;
            
            // Mover la nueva imagen
            if(move_uploaded_file($tempPath, $dir)) {
                // Eliminar la imagen anterior si existe
                if(file_exists("foods-images/" . $food->image)) {
                    unlink("foods-images/" . $food->image);
                }
                $image = $uniqueName;
                $updateImage = true;
            } else {
                echo "<script>alert('Error: No se pudo guardar la nueva imagen.');</script>";
            }
        }
    }

    // Preparar query de actualización
    if($updateImage) {
        $query = "UPDATE foods SET name = :name, price = :price, description = :description, 
                 meal_id = :meal_id, image = :image WHERE id = :id";
        $arr = [
            ":name" => $name,
            ":price" => $price,
            ":description" => $description,
            ":meal_id" => $meal_id,
            ":image" => $image,
            ":id" => $id
        ];
    } else {
        $query = "UPDATE foods SET name = :name, price = :price, description = :description, 
                 meal_id = :meal_id WHERE id = :id";
        $arr = [
            ":name" => $name,
            ":price" => $price,
            ":description" => $description,
            ":meal_id" => $meal_id,
            ":id" => $id
        ];
    }

    $path = "show-foods.php";
    
    // Ejecutar la actualización
    $app->register($query, $arr, $path);
    
   
}

?>
       <div class="row">
        <div class="col">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title mb-5 d-inline">Edit Food Item</h5>
          <form method="POST" action="edit-foods.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
                <!-- Name input -->
                <div class="form-outline mb-4 mt-4">
                  <input type="text" value="<?php echo htmlspecialchars($food->name); ?>" name="name" id="form2Example1" class="form-control" placeholder="name" required />
                </div>
                
                <!-- Price input -->
                <div class="form-outline mb-4 mt-4">
                  <input type="number" value="<?php echo htmlspecialchars($food->price); ?>" step="0.01" name="price" id="form2Example1" class="form-control" placeholder="price" required />
                </div>
                
                <!-- Image input -->
                <div class="form-outline mb-4 mt-4">
                  <label for="image">Current Image:</label>
                  <img src="foods-images/<?php echo $food->image; ?>" style="width: 100px; height: 100px; display: block; margin-bottom: 10px;">
                  <label for="image">New Image (leave empty to keep current):</label>
                  <input type="file" name="image" id="image" class="form-control" />
                </div>
                
                <!-- Description input -->
                <div class="form-group">
                  <label for="exampleFormControlTextarea1">Description</label>
                  <textarea name="description" class="form-control" id="exampleFormControlTextarea1" rows="3" required><?php echo htmlspecialchars($food->description); ?></textarea>
                </div>
               
                <!-- Meal type select -->
                <div class="form-outline mb-4 mt-4">
                  <select name="meal_id" class="form-select form-control" aria-label="Default select example" required>
                    <option value="1" <?php echo $food->meal_id == 1 ? 'selected' : ''; ?>>Breakfast</option>
                    <option value="2" <?php echo $food->meal_id == 2 ? 'selected' : ''; ?>>Launch</option>
                    <option value="3" <?php echo $food->meal_id == 3 ? 'selected' : ''; ?>>Dinner</option>
                  </select>
                </div>

                <br>
              
                <!-- Submit button -->
                <button type="submit" name="submit" class="btn btn-primary mb-4 text-center">Update Food</button>
                <a href="show-foods.php" class="btn btn-secondary mb-4 text-center">Cancel</a>

              </form>

            </div>
          </div>
        </div>
      </div>
<?php require "../layouts/footer.php"; ?>