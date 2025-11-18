<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Menú Lateral con Logo</title>
  <style>
    body { 
        font-family: Arial, sans-serif; 
        margin: 0; 
          background-color: beige; /* Cambiado a beige */
    } 

    /* Barra superior */
    .navbar{
      background-color: rgb(0, 162, 255); color: white; padding: 1rem;
      display:flex; justify-content:center; align-items:center; position:relative;
    }
    .logo img{ width:150px; height:100px; object-fit:contain; }

    /* Menú hamburguesa a la izquierda */
    .menu-icon{
      position:absolute; left:1rem;
      font-size:28px; cursor:pointer;
    }

    /* Botones a la derecha */
    .auth-buttons{
      position:absolute; right:1rem; display:flex; align-items:center; gap:10px;
    }
    .auth-buttons button{
      padding:6px 12px;
      border:none; border-radius:5px;
      cursor:pointer;
      font-weight:bold;
    }
    .auth-buttons .register{ background:white; color:rgb(0, 162, 255); }
    .auth-buttons .login{ background:#1e90ff; color:white; }
    .auth-buttons button:hover{ opacity:0.85; }

    /* Sidebar */
    .sidebar{
      position:fixed; top:0; left:-250px; width:250px; height:100%;
      background: rgb(0, 162, 255); padding-top:60px; transition:left 0.3s ease-in-out;
      display:flex; flex-direction:column;
      z-index:1001;
    }
    .sidebar a, .sidebar p{
      padding:15px; text-decoration:none; color:white; display:block;
      border-bottom:1px solid #555;
      transition: background 0.25s ease, padding-left 0.25s ease;
      cursor:pointer;
      margin:0;
    }
    .sidebar a:hover{ background:#1e90ff; padding-left:25px; }
    .sidebar.active{ left:0; }

    /* Overlay */
    .overlay{
      position:fixed; top:0; left:0; width:100%; height:100%;
      background:rgba(0,0,0,0.5); display:none;
      z-index:1000;
    }
    .overlay.active{ display:block; }

    /* Estilo para modales */
    .modal{
      display:none; position:fixed; top:0; left:0; width:100%; height:100%;
      background:rgba(0,0,0,0.5); z-index:2000; justify-content:center; align-items:center;
    }
    .modal-content{
      background:white; padding:20px; border-radius:10px; width:300px;
      box-shadow:0 5px 15px rgba(0,0,0,0.3); text-align:center; position:relative;
    }
    .modal-content h2{ margin-top:0; }
    .close{
      position:absolute; top:10px; right:15px; font-size:18px; cursor:pointer; font-weight:bold;
    }
    .modal input{
      width:90%; padding:8px; margin:10px 0; border:1px solid #ccc; border-radius:5px;
    }
    .modal button{
      background:rgb(0,162,255); color:white; border:none; padding:8px 15px; border-radius:5px; cursor:pointer;
    }
    .modal button:hover{ background:#1e90ff; }

   .ventanita{
  background: white; /* Fondo beige */
  border-radius: 10px;
  margin: 20px auto;
  padding: 20px;
  width: 90%;
  max-width: 1200px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.contenido-secundario{
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 20px;
}

.texto{
  flex: 2;
  text-align: left; /* texto alineado a la izquierda */
}

.texto p{
  margin-bottom: 20px;
}

.imagen-coop{
  flex: 1;
  max-width: 40%;
  height: auto;
  border-radius: 8px;
}


  </style>
</head>
<body>
  <div class="navbar">
    <!-- Menú hamburguesa a la izquierda -->
    <span class="menu-icon" onclick="toggleMenu()">&#9776;</span>

    <!-- Logo centrado -->
    <div class="logo"><img src="logo.png" alt="Logo"></div>

    <!-- Botones a la derecha -->
    <div class="auth-buttons">
      <button class="register" onclick="openModal('registerModal')">Registro</button>
      <button class="login" onclick="openModal('loginModal')">Iniciar Sesión</button>
    </div>
  </div>

  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <a href="#">Whatsapp</a>
    <a href="#">Gmail</a>
    <p>Tel: 097 393 688</p>
  </div>

  <!-- Fondo oscuro sidebar -->
  <div class="overlay" id="overlay" onclick="toggleMenu()"></div>

  <!-- Modal Registro -->
  <div class="modal" id="registerModal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('registerModal')">&times;</span>
      <h2>Registro</h2>
      <form action="register.php" method="post">
        <label>Nombre completo<br><input name="nombre" required></label>
        <label>Cédula<br><input name="idUser" required></label>
        <label>Telefono<br><input name="telefono" required></label>
        <label>Gmail<br><input name="email" required></label>
        <label>Fecha de nacimiento<br><input name="fechNac" type="date" required></label>
        <label>Contraseña<br><input name="password" type="password" required></label>
        <button type="submit">Registrarse</button></form>
    </div>
  </div>

  <!-- Modal Login -->
  <div class="modal" id="loginModal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('loginModal')">&times;</span>
      <h2>Iniciar Sesión</h2>
      <form action="login.php" method="post">
      <input name="idUser" placeholder="Cedula" required>
      <input name="password" placeholder="Contraseña" required>
      <button type="submit">Entrar</button>
      </form>
    </div>
  </div>

    <div class="ventanita">
  <!-- Contenedor principal -->
  <div class="contenido-secundario">
    
    <!-- Texto (párrafo + lista) -->
    <div class="texto">
      <p>
        Las cooperativas son asociaciones de personas que se unen de forma voluntaria y democrática
        para satisfacer necesidades comunes y alcanzar objetivos compartidos.<br><br>
        En lugar de priorizar el beneficio individual, las cooperativas se basan en los valores de la solidaridad,
        la ayuda mutua, la equidad y la transparencia.<br><br>
        En una cooperativa de vivienda por ayuda mutua, cada socio aporta no solo recursos económicos,
        sino también su tiempo y esfuerzo en jornadas de trabajo colectivo. Esto fortalece el sentido de comunidad,
        fomenta la igualdad y permite acceder a una vivienda digna de manera justa y compartida.<br><br>
        Nuestro sistema de gestión acompaña este espíritu cooperativo, brindando a cada socio las herramientas necesarias para:
      </p>

      <ul>
        <li>Participar activamente en la vida de la cooperativa.</li>
        <li>Acceder de manera transparente a la información de aportes, horas de trabajo y decisiones.</li>
        <li>Reforzar la confianza y la unión entre los miembros de la comunidad.</li>
      </ul>
    </div>

    <!-- Imagen -->
    <img src="coop.png" alt="Cooperativa" class="imagen-coop">
  </div>
</div>



  <script>
    // Sidebar
    function toggleMenu() {
      document.getElementById("sidebar").classList.toggle("active");
      document.getElementById("overlay").classList.toggle("active");
    }

    // Abrir modal
    function openModal(id) {
      document.getElementById(id).style.display = "flex";
    }

    // Cerrar modal
    function closeModal(id) {
      document.getElementById(id).style.display = "none";
    }

    // Cerrar modal al hacer clic fuera
    window.onclick = function(e) {
      if (e.target.classList.contains("modal")) {
        e.target.style.display = "none";
      }
    }
  </script>
</body>
</html>
