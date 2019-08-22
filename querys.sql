--alles equipment, inkl set und buchung

SELECT equipment.equipment_id, 
equipment.name AS eq_name, 
equipment.beschrieb AS eq_beschrieb, 
set_.set_id, 
set_.beschrieb AS set_beschrieb, 
set_.name AS set_name, 
buchung.buchung_id, 
buchung.reserviert_fuer, 
buchung.user, 
equipmentbild.filename,
kategorie.name AS kat_name
FROM equipment LEFT JOIN set_ 
ON equipment.set_id = set_.set_id LEFT JOIN buchung 
ON equipment.equipment_id = buchung.equipment_id LEFT JOIN kategorie 
ON equipment.kategorie_id = kategorie.kategorie_id LEFT JOIN equipmentbild
ON equipment.bild_id = equipmentbild.bild_id WHERE equipment.geloescht=false AND (buchung.reserviert_fuer >= NOW() OR buchung.reserviert_fuer IS NULL) ORDER BY set_.name DESC