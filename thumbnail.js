var gallery = {
  show : function(img) {
  // show() : show selected image in light box

    var clone = img.cloneNode(),
        domain = clone.src.substr(0, clone.src.lastIndexOf("/",clone.src.lastIndexOf("/")-1)+1),
        image = clone.src.substr(clone.src.lastIndexOf("/")+1),
        front = document.getElementById("lfront"),
        back = document.getElementById("lback");

    clone.src = domain + "gallery/" + image;
    front.innerHTML = "";
    front.appendChild(clone);
    back.classList.add("show");
  },

  hide : function() {
  // hide() : hide the lightbox

    document.getElementById("lback").classList.remove("show");
  },

  showdiv : function(input) {
  // selectdiv(): show only the image according to the selected period

    this.hideeverything();
    var y = input.value,
        showndiv = document.getElementById(y);
    /* showndiv.style.display = "block";   */
    showndiv.hidden = false;
  },

  hideeverything : function() {
    var nonfiltereddivs = document.getElementsByClassName("gallery");
    alldivs = Array.prototype.filter.call(nonfiltereddivs, function(testElement){
      return testElement.nodeName === 'DIV';
  });
  
  for (var div in alldivs) {
/*       console.log(alldivs);
      console.log(alldivs[div]); */
      alldivs[div].hidden = true;
    }
  }
};

gallery.hideeverything();
document.getElementById('0').hidden = false;
