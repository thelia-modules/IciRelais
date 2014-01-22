#!/usr/bin/env python
# -*- coding: utf-8 -*-

xml_forms = []
forms=[]
db=[]



# Form: SearchCityForm
xml_forms.append(("SearchCityForm","icirelais.searchcity"))
form = [ "SearchCityForm", "folder-edit.html", [["text", "ville", "ville", "NotBlank"],["[text", "postal", "postal", "NotBlank"],[""]] ]
forms.append(form)
# Form: SearchCityForm
xml_forms.append(("SearchCityForm","icirelais.searchcity"))
form = [ "SearchCityForm", "folder-edit.html", [["text", "ville", "ville", "NotBlank"],["[text", "postal", "postal", "NotBlank"],[""]] ]
forms.append(form)
# Form: SearchCityForm
xml_forms.append(("SearchCityForm","icirelais.searchcity"))
form = [ "SearchCityForm", "folder-edit.html", [["text", "ville", "ville", "NotBlank"],["[text", "postal", "postal", "NotBlank"],[""]] ]
forms.append(form)
# Form: SearchCityForm
xml_forms.append(("SearchCityForm","icirelais.search"))
form = [ "SearchCityForm", "tax-rule-edit.html", [["test", "city", "city", "NotBlank"],["[test", "postal", "postal", "NotBlank"],[""]] ]
forms.append(form)