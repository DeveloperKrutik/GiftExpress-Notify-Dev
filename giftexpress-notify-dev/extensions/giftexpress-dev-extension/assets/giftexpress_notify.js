var elements = document.getElementsByClassName('giftexpress_notify_input_field');
var addtocartforms = document.querySelectorAll('form[action="/cart/add"]');

for (var i = 0; i < elements.length; i++) {
  var inputValue = elements[i].value; 
  var datacategory = elements[i].getAttribute('data-category');

  addtocartforms.forEach(function(addtocartform) {
    addtocartform.innerHTML += '<input type="hidden" class="giftexpress_notify_'+datacategory+'" name="properties['+datacategory+']">';
  });
}

for (var i = 0; i < elements.length; i++) {
  elements[i].addEventListener('input', function(event) {
    var inputValue = this.value; 
    var datacategory = this.getAttribute('data-category');
    var formele = document.getElementsByClassName('giftexpress_notify_'+datacategory);
    for (var j = 0; j < formele.length; j++) {
      formele[j].value = inputValue;
    }
  });
}