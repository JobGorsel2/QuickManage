 
function closeMenu() {
    console.log('close');
    var menu = document.getElementsByClassName('menu');
    var ham_menu = document.getElementsByClassName('ham_menu');
    
    for (var i = 0; i < menu.length; i++) {
      menu[i].style.display = "none";
 
    }
    
    for (var i = 0; i < ham_menu.length; i++) {
      ham_menu[i].style.display = "block";
 
    }
  }

  function openMenu() {
    console.log('open');
   
    var menu = document.getElementsByClassName('menu');
    var ham_menu = document.getElementsByClassName('ham_menu');
    
    for (var i = 0; i < menu.length; i++) {
      menu[i].style.display = "block";
       
    }
    
    for (var i = 0; i < ham_menu.length; i++) {
      ham_menu[i].style.display = "none";
 
    }
  }