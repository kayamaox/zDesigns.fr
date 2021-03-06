/*! 
 * textarea tools 1.0 based on a-tools 1.2

 * a-tools 1.2
 * Copyright (c) 2009 Andrey Kramarev, Ampparit Inc. (www.ampparit.com)
 * Licensed under the MIT license.
 * http://www.ampparit.fi/a-tools/license.txt
 *
 
 * textarea tools 1.0
 * Copyright (c) 2010 Fabien Calluaud (smurf1.free.fr, tm-ladder.com)
 * Licensed under the MIT license.
 * http://smurf1.free.fr/sdz/licence.txt
 *
 * Add function wrapSelection and insertAtCaret2
 
 
 * Basic usage:
 
    <textarea></textarea>
    <input type="text" />

    // Get current selection
    var sel = $("textarea").getSelection()
    
    // Replace current selection
    $("input").replaceSelection("foo");

    // Count characters
    alert($("textarea").countCharacters());

    // Set max length without callback function
    $("textarea").setMaxLength(7);

    // Set max length with callback function which will be called when limit is exceeded
    $("textarea").setMaxLength(10, function() {
        alert("hello")
    });

    // Removing limit:
    $("textarea").setMaxLength(-1);
    
    // Insert text at current caret position    
    $("#textarea").insertAtCaretPos("hello"); <- bug on IE7
	
	$("#textarea").insertAtCaretPos2("hello"); <- works on IE and FF
    
    // Set caret position (1 = beginning, -1 = end)
    $("#textArea").setCaretPos(10);
		
	// wrap the selection with the 2 given parameters
	// if there is no selection, execute insertAtCaretPos(param1+param2)
	$("#textArea").wrapSelection("before", "after");

 */
 
 
 var caretPositionAmp;
jQuery.fn.extend({
	wrapSelection: function (a, b) {
		var scroll = this.scrollTop();
		if (this.getSelection().length) {
			var sel = this.getSelection();
			this.replaceSelection(a + this.getSelection().text + b);
			this.setCaretPos(sel.start + a.length + sel.text.length + 1);
		} else {
			this.insertAtCaretPos2(a + b);
		}
		this.scrollTop(scroll);
	},
	getSelection: function () {
		var k = this.jquery ? this[0] : this;
		var b;
		var f;
		var c;
		if (document.selection) {
			var p = document.selection.createRange();
			if (p.text) {
				var d = 0;
				c = p.text;
				if (k.value.match(/\n/g) != null) {
					d = k.value.match(/\n/g).length
				}
				var e = 0;
				var j = 0;
				var g = 0;
				if (typeof(k.selectionStart) == "number") {
					b = k.selectionStart;
					f = k.selectionEnd;
					if (b == f) {
						return this
					}
				} else {
					var n = k.createTextRange();
					var m;
					var o;
					var a = n.duplicate();
					m = n.text;
					n.moveToBookmark(p.getBookmark());
					o = n.text;
					a.setEndPoint("EndToStart", n);
					if (m == o && m != p.text) {
						return this
					}
					b = a.text.length;
					f = a.text.length + p.text.length
				}
				if (d > 0) {
					for (var h = 0; h <= d; h++) {
						var l = k.value.indexOf("\n", j);
						if (l != -1 && l < b) {
							j = l + 1;
							e++;
							g = e
						} else {
							if (l != -1 && l >= b && l <= f) {
								if (l == b + 1) {
									e--;
									g--;
									j = l + 1;
									continue
								}
								j = l + 1;
								g++
							} else {
								h = d
							}
						}
					}
				}
				if (p.text.indexOf("\n", 0) == 1) {
					g = g + 2
				}
				b = b - e;
				f = f - g;
				return {
					start: b,
					end: f,
					text: p.text,
					length: f - b
				}
			}
			return false
		} else {
			if (typeof(k.selectionStart) == "number" && k.selectionStart != k.selectionEnd) {
				b = k.selectionStart;
				f = k.selectionEnd;
				c = k.value.substring(k.selectionStart, k.selectionEnd);
				return {
					start: b,
					end: f,
					text: c,
					length: f - b
				}
			} else {
				return {
					start: undefined,
					end: undefined,
					text: undefined,
					length: undefined
				}
			}
		}
	},
	replaceSelection: function (h) {
		var j = this.jquery ? this[0] : this;
		var b;
		var e;
		var g = 0;
		var a;
		var m;
		var d = 0;
		var c = 0;
		if (document.selection && typeof(j.selectionStart) != "number") {
			var o = document.selection.createRange();
			if (typeof(j.selectionStart) != "number") {
				var l;
				var n;
				m = j.createTextRange();
				a = m.duplicate();
				l = m.text;
				m.moveToBookmark(o.getBookmark());
				n = m.text;
				a.setEndPoint("EndToStart", m);
				if (l == n && l != o.text) {
					return this
				}
			}
			if (o.text) {
				part = o.text;
				if (j.value.match(/\n/g) != null) {
					d = j.value.match(/\n/g).length
				}
				b = a.text.length;
				if (d > 0) {
					for (var f = 0; f <= d; f++) {
						var k = j.value.indexOf("\n", g);
						if (k != -1 && k < b) {
							g = k + 1;
							c++
						} else {
							f = d
						}
					}
				}
				o.text = h;
				caretPositionAmp = a.text.length + h.length;
				m.move("character", caretPositionAmp);
				document.selection.empty();
				j.blur()
			}
			return this
		} else {
			if (typeof(j.selectionStart) == "number" && j.selectionStart != j.selectionEnd) {
				b = j.selectionStart;
				e = j.selectionEnd;
				j.value = j.value.substr(0, b) + h + j.value.substr(e);
				g = b + h.length;
				j.setSelectionRange(g, g);
				return this
			}
		}
		return this
	},
	insertAtCaretPos: function (g) {
		var h = this.jquery ? this[0] : this;
		var b;
		var d;
		var f;
		var m;
		var l;
		var a;
		var k;
		var c = 0;
		h.focus();
		if (document.selection && typeof(h.selectionStart) != "number") {
			if (h.value.match(/\n/g) != null) {
				number = h.value.match(/\n/g).length
			}
			k = parseInt(caretPositionAmp);
			if (number > 0) {
				for (var e = 0; e <= number; e++) {
					var j = h.value.indexOf("\n", f);
					if (j != -1 && j <= k) {
						f = j + 1;
						k = k - 1;
						c++
					}
				}
			}
		}
		caretPositionAmp = parseInt(caretPositionAmp);
		h.onclick = function () {
			if (document.selection && typeof(h.selectionStart) != "number") {
				m = document.selection.createRange();
				l = h.createTextRange();
				a = l.duplicate();
				l.moveToBookmark(m.getBookmark());
				a.setEndPoint("EndToStart", l);
				caretPositionAmp = a.text.length
			}
		};
		if (document.selection && typeof(h.selectionStart) != "number") {
			m = document.selection.createRange();
			if (m.text.length != 0) {
				return this
			}
			l = h.createTextRange();
			textLength = l.text.length;
			a = l.duplicate();
			l.moveToBookmark(m.getBookmark());
			a.setEndPoint("EndToStart", l);
			b = a.text.length;
			if (caretPositionAmp >= 0 && b == 0 && caretPositionAmp != b) {
				c = caretPositionAmp - c;
				l.move("character", c);
				l.select();
				m = document.selection.createRange();
				caretPositionAmp += g.length
			} else {
				if (caretPositionAmp == undefined && b == 0) {
					l.move("character", textLength);
					l.select();
					m = document.selection.createRange();
					caretPositionAmp = g.length
				} else {
					if (! (parseInt(caretPositionAmp) >= 0)) {
						l.move("character", b);
						document.selection.empty();
						l.select();
						m = document.selection.createRange();
						caretPositionAmp = b + g.length
					} else {
						if (parseInt(caretPositionAmp) >= 0) {
							l.move("character", caretPositionAmp - b);
							document.selection.empty();
							l.select();
							m = document.selection.createRange();
							caretPositionAmp = caretPositionAmp + g.length
						} else {
							l.move("character", caretPositionAmp - b);
							document.selection.empty();
							l.select();
							m = document.selection.createRange();
							caretPositionAmp = caretPositionAmp + g.length
						}
					}
				}
			}
			m.text = g;
			h.focus();
			return this
		} else {
			if (typeof(h.selectionStart) == "number" && h.selectionStart == h.selectionEnd) {
				f = h.selectionStart + g.length;
				b = h.selectionStart;
				d = h.selectionEnd;
				h.value = h.value.substr(0, b) + g + h.value.substr(d);
				h.setSelectionRange(f, f);
				return this
			}
		}
		return this
	},
	insertAtCaretPos2: function (g) {
		if(document.selection) {
			this.focus();
			var sel = document.selection.createRange();
			sel.text=g;
			this.focus();
		}
		else if(this.attr("selectionStart") || this.attr("selectionStart")=="0") {
			var startPos = this.attr("selectionStart"); var endpos = this.selectionEnd;
			this.attr("value", this.attr("value").substring(0, startPos)+ g + this.attr("value").substring(startPos, this.attr("value").length));
			this.focus();
			this.attr("selectionStart", startPos + g.length);
			this.attr("selectionEnd", startPos + g.length);
		}
		else {
			this.value += g;
			this.focus();
		}
	},
	setCaretPos: function (e) {
		var f = this.jquery ? this[0] : this;
		var j;
		var h;
		var d;
		var a;
		var b = 0;
		var g;
		f.focus();
		if (parseInt(e) == 0) {
			return this
		}
		if (parseInt(e) > 0) {
			e = parseInt(e) - 1;
			if (document.selection && typeof(f.selectionStart) == "number" && f.selectionStart == f.selectionEnd) {
				if (f.value.match(/\n/g) != null) {
					a = f.value.match(/\n/g).length
				}
				if (a > 0) {
					for (var c = 0; c <= a; c++) {
						g = f.value.indexOf("\n", d);
						if (g != -1 && g <= e) {
							d = g + 1;
							e = parseInt(e) + 1
						}
					}
				}
			}
		} else {
			if (parseInt(e) < 0) {
				e = parseInt(e) + 1;
				if (document.selection && typeof(f.selectionStart) != "number") {
					e = f.value.length + parseInt(e);
					if (f.value.match(/\n/g) != null) {
						a = f.value.match(/\n/g).length
					}
					if (a > 0) {
						for (var c = 0; c <= a; c++) {
							g = f.value.indexOf("\n", d);
							if (g != -1 && g <= e) {
								d = g + 1;
								e = parseInt(e) - 1;
								b += 1
							}
						}
						e = e + b - a
					}
				} else {
					if (document.selection && typeof(f.selectionStart) == "number") {
						e = f.value.length + parseInt(e);
						if (f.value.match(/\n/g) != null) {
							a = f.value.match(/\n/g).length
						}
						if (a > 0) {
							e = parseInt(e) - a;
							for (var c = 0; c <= a; c++) {
								g = f.value.indexOf("\n", d);
								if (g != -1 && g <= (e)) {
									d = g + 1;
									e = parseInt(e) + 1;
									b += 1
								}
							}
						}
					} else {
						e = f.value.length + parseInt(e)
					}
				}
			} else {
				return this
			}
		}
		if (document.selection && typeof(f.selectionStart) != "number") {
			j = document.selection.createRange();
			if (j.text != 0) {
				return this
			}
			h = f.createTextRange();
			h.moveToBookmark(j.getBookmark());
			h.move("character", e);
			h.select();
			j = document.selection.createRange();
			j.text = "";
			caretPositionAmp = e;
			f.focus();
			return this
		} else {
			if (typeof(f.selectionStart) == "number" && f.selectionStart == f.selectionEnd) {
				f.setSelectionRange(e, e);
				return this
			}
		}
		return this
	},
	countCharacters: function (b) {
		var a = this.jquery ? this[0] : this;
		if (a.value.match(/\r/g) != null) {
			return a.value.length - a.value.match(/\r/g).length
		}
		return a.value.length
	},
	setMaxLength: function (a, b) {
		this.each(function () {
			var d = this.jquery ? this[0] : this;
			var f = d.type;
			var e;
			var c;
			if (parseInt(a) < 0) {
				a = 100000000
			}
			if (f == "text") {
				d.maxLength = a
			}
			if (f == "textarea" || f == "text") {
				d.onkeypress = function (j) {
					var g = d.value.match(/\r/g);
					c = a;
					if (g != null) {
						c = parseInt(c) + g.length
					}
					var h = j || event;
					var i = h.keyCode;
					if (document.selection) {
						e = document.selection.createRange().text.length > 0
					} else {
						e = d.selectionStart != d.selectionEnd
					}
					if (d.value.length >= c && (i > 47 || i == 32 || i == 0 || i == 13) && !h.ctrlKey && !h.altKey && !e) {
						d.value = d.value.substring(0, c);
						if (typeof(b) == "function") {
							b()
						}
						return false
					}
				};
				d.onkeyup = function () {
					var h = d.value.match(/\r/g);
					var k = 0;
					var g = 0;
					c = a;
					if (h != null) {
						for (var j = 0; j <= h.length; j++) {
							if (d.value.indexOf("\n", g) <= parseInt(a)) {
								k++;
								g = d.value.indexOf("\n", g) + 1
							}
						}
						c = parseInt(a) + k
					}
					if (d.value.length > c) {
						d.value = d.value.substring(0, c);
						if (typeof(b) == "function") {
							b()
						}
						return this
					}
				}
			} else {
				return this
			}
		});
		return this
	}
});