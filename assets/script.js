function includeHTML() {
    $("#main").load(file)

  }
  $('.arrow').click(function(){
    let element = $('#arrow');
    
    (element.attr('class')=='fas fa-arrow-left') ? element.attr('class','fas fa-arrow-right'):element.attr('class','fas fa-arrow-left');
    
  })