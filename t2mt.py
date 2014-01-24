#!/usr/bin/env python
# -*- coding: utf-8 -*-

xml_forms = []
forms=[]
db=[]

# Form: ExportExaprint
xml_forms.append(("ExportExaprint","icirelais.export"))
form = [ "ExportExaprint", "module-edit.html", [["text", "name", "name"],["[text", "addr", "addr"],["text", "addr2", "addr2"],["text", "zipcode", "zipcode"],["text", "city", "city"],["text", "tel", "tel"],["text", "mobile", "mobile"],["text", "mail", "mail"],["radio", "assur", "assur"],[""]] ]
forms.append(form)
# Form: ExportExaprint
xml_forms.append(("ExportExaprint","icirelais.export"))
form = [ "ExportExaprint", "module-edit.html", [["text", "name", "name", "NotBlank"],["[text", "addr", "addr", "NotBlank"],["text", "addr2", "addr2", "NotBlank"],["text", "zipcode", "zipcode", "NotBlank"],["text", "city", "city", "NotBlank"],["text", "tel", "tel"],["text", "mobile", "mobile"],["text", "mail", "mail", "NotBlank"],["radio", "assur", "assur", "NotBlank"],[""]] ]
forms.append(form)