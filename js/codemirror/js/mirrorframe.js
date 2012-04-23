/* Demonstration of embedding CodeMirror in a bigger application. The
 * interface defined here is a mess of prompts and confirms, and
 * should probably not be used in a real project.
 */

function MirrorFrame(place, options) {
  this.home = document.createElement("div");
  if (place.appendChild)
    place.appendChild(this.home);
  else
    place(this.home);

  var self = this;
  function makeButton(name, action) {
    var button = document.createElement("input");
    button.type = "button";
    button.value = name;
    self.home.appendChild(button);
    button.onclick = function(){self[action].call(self);};
  }

  makeButton("Rechercher", "search");
  makeButton("Remplacer", "replace");
  makeButton("Aller à la ligne", "jump");
  makeButton("Indenter le code", "reindent");

  this.mirror = new CodeMirror(this.home, options);
  return this;
}

MirrorFrame.prototype = {
  search: function() {
    var text = prompt("Chaine recherchée :", "");
    if (!text) return;

    var first = true;
    do {
      var cursor = this.mirror.getSearchCursor(text, first);
      first = false;
      while (cursor.findNext()) {
        cursor.select();
        if (!confirm("Aller au suivant ?"))
          return;
      }
    } while (confirm("Fin du document atteinte. Retourner au début et continuer ?"));
  },

  replace: function() {
    // This is a replace-all, but it is possible to implement a
    // prompting replace.
    var from = prompt("Chaine recherchée :", ""), to;
    if (from) to = prompt("Remplacer cette chaine par :", "");
    if (to == null) return;

    var cursor = this.mirror.getSearchCursor(from, false);
    while (cursor.findNext())
      cursor.replace(to);
  },

  jump: function() {
    var line = prompt("Aller à la ligne :", "");
    if (line && !isNaN(Number(line)))
      this.mirror.jumpToLine(Number(line));
  },

  line: function() {
    alert("The cursor is currently at line " + this.mirror.currentLine());
    this.mirror.focus();
  },

  macro: function() {
    var name = prompt("Name your constructor:", "");
    if (name)
      this.mirror.replaceSelection("function " + name + "() {\n  \n}\n\n" + name + ".prototype = {\n  \n};\n");
  },

  reindent: function() {
    this.mirror.reindent();
  }
};
