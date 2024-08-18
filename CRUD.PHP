<?php
// Datos de conexión
$host = "localhost";
$dbname = "gestion_inventario";
$user = "";
$password = "";

try {
    $dsn = "pgsql:host=$host;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "Conexión exitosa!";
} catch (PDOException $e) {
    echo "Error al conectar a PostgreSQL: " . $e->getMessage();
    exit(); // Asegúrate de salir si la conexión falla
}

// Funciones CRUD
function insertarProducto($pdo, $nombre) {
    try {
        $sql = "INSERT INTO productos (nombre) VALUES (:nombre)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['nombre' => $nombre]);
        return "Producto agregado correctamente.";
    } catch (PDOException $e) {
        return "Error al insertar producto: " . $e->getMessage();
    }
}

function actualizarProducto($pdo, $nombre, $id) {
    try {
        $sql = "UPDATE productos SET nombre = :nombre WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['nombre' => $nombre, 'id' => $id]);
        return "Producto actualizado correctamente.";
    } catch (PDOException $e) {
        return "Error al actualizar producto: " . $e->getMessage();
    }
}

function eliminarProducto($pdo, $nombre) {
    try {
        $sql = "DELETE FROM productos WHERE nombre = :nombre";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['nombre' => $nombre]);
        return "Producto eliminado correctamente.";
    } catch (PDOException $e) {
        return "Error al eliminar producto: " . $e->getMessage();
    }
}

function verProducto($pdo, $id) {
    try {
        $sql = "SELECT * FROM productos WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($producto) {
            return "ID: " . $producto['id'] . " - Nombre: " . $producto['nombre'];
        } else {
            return "Producto no encontrado.";
        }
    } catch (PDOException $e) {
        return "Error al ver producto: " . $e->getMessage();
    }
}

// Manejo de formularios
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    $message = ''; // Inicializar el mensaje
    
    switch ($action) {
        case 'add':
            if (isset($_POST['add-product'])) {
                $message = insertarProducto($pdo, $_POST['add-product']);
            }
            break;

        case 'update':
            if (isset($_POST['update-product']) && isset($_POST['update-id'])) {
                $message = actualizarProducto($pdo, $_POST['update-product'], $_POST['update-id']);
            }
            break;

        case 'delete':
            if (isset($_POST['delete-product'])) {
                $message = eliminarProducto($pdo, $_POST['delete-product']);
            }
            break;

        case 'view':
            if (isset($_POST['view-id'])) {
                $message = verProducto($pdo, $_POST['view-id']);
            }
            break;
    }

    // Redirigir a crud.html con el mensaje
    header("Location: crud.html?message=" . urlencode($message));
    exit();
}
?>
