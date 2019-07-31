function ajax(element, source_file) {
  var xhr = new XMLHttpRequest();

  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
      element.innerHTML = xhr.responseText;
    }
  }

  xhr.open('GET', source_file, true);
  xhr.send();
}

var ckPwd = document.querySelector('input#ckPwd');
var pwd = document.querySelectorAll('input.pwd');
var btnResetLogin = document.querySelector('input[type="reset"]#btnResetLogin');

if (ckPwd != null) {
  ckPwd.addEventListener('change', function showPassword() {
    if (ckPwd.checked == true) {
      pwd[0].setAttribute('type', 'text');
      pwd[1].setAttribute('type', 'text');
      pwd[2].setAttribute('type', 'text');
    } else {
      pwd[0].setAttribute('type', 'password');
      pwd[1].setAttribute('type', 'password');
      pwd[2].setAttribute('type', 'password');
    }
  });
}

if (btnResetLogin) {
  btnResetLogin.addEventListener('click', function() {
    pwd.setAttribute('value', '');
  });
}

// Event Listener for object tb_prov -> input_formulir_pp.php
var opProv = document.getElementById("op_prov");

if (opProv != null) {
  var opKab = document.getElementById("op_kabupaten");
  var opKec = document.getElementById("op_kecamatan");
  var opKel = document.getElementById("op_kelurahan");

  opProv.addEventListener('change', function () {
    ajax(opKab, 'opsi_wilayah.php?p=' + opProv.value);
  });

  opKab.addEventListener('change', function () {
    ajax(opKec, 'opsi_wilayah.php?kab=' + opKab.value);
  });

  opKec.addEventListener('change', function () {
    ajax(opKel, 'opsi_wilayah.php?kec=' + opKec.value);
  });
  
}  // Akhir validasi objek input alamat.

// EventListener untuk tombol kembali di form ubah_data_customer.php
var btKembaliudc = document.getElementById("btKembali");
if (btKembaliudc != null) {
  btKembaliudc.addEventListener("click", function(e) {
    var ask = window.confirm("Batalkan perubahan?");
    if (ask) {
        window.location.href = btKembaliudc.className;       
    }
  });
}

var tx = Array.from(document.querySelectorAll('.text-jenis-lain'));
if (tx != null) {
  tx.forEach(function(elm) {
      elm.addEventListener('change', function() {
          if (elm.value.trim().length > 0) {
            if (elm.parentNode.children[0].children[0].checked == false) {
              elm.parentNode.children[0].children[0].checked = true;
            }
            elm.parentNode.children[0].children[0].value = elm.value;
          } else {
            if (elm.parentNode.children[0].children[0].checked == true) {
              elm.parentNode.children[0].children[0].checked = false;
            }
          }
      });
  });
}