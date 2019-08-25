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
ON equipment.bild_id = equipmentbild.bild_id 
WHERE equipment.geloescht=false AND equipment.aktiv=true 
AND (buchung.reserviert_fuer = DATE(NOW()) 
OR buchung.reserviert_fuer IS NULL) ORDER BY set_.name DESC

--alles equipment, inkl set ohne Buchung

SELECT 
equipment.equipment_id, 
equipment.name AS eq_name, 
equipment.beschrieb AS eq_beschrieb, 
set_.set_id, 
set_.beschrieb AS set_beschrieb, 
set_.name AS set_name, 
equipmentbild.filename,
kategorie.name AS kat_name,
equipment.aktiv
FROM equipment LEFT JOIN set_ 
ON equipment.set_id = set_.set_id LEFT JOIN kategorie 
ON equipment.kategorie_id = kategorie.kategorie_id LEFT JOIN equipmentbild
ON equipment.bild_id = equipmentbild.bild_id 
WHERE equipment.geloescht=false
ORDER BY set_.name DESC;


SELECT 
equipment.equipment_id, 
equipment.name AS eq_name, 
equipment.beschrieb AS eq_beschrieb, 
set_.set_id, 
set_.beschrieb AS set_beschrieb, 
set_.name AS set_name, 
equipmentbild.filename,
kategorie.name AS kat_name,
equipment.aktiv
FROM equipment LEFT JOIN set_ 
ON equipment.set_id = set_.set_id LEFT JOIN kategorie 
ON equipment.kategorie_id = kategorie.kategorie_id LEFT JOIN equipmentbild
ON equipment.bild_id = equipmentbild.bild_id 
WHERE equipment.geloescht=false
GROUP BY set_.name DESC;


--equipment alleine
SELECT 
equipment.equipment_id, 
equipment.name,
equipment.beschrieb,
equipmentbild.filename,
kategorie.name AS kat_name,
equipment.aktiv
FROM equipment LEFT JOIN kategorie 
ON equipment.kategorie_id = kategorie.kategorie_id LEFT JOIN equipmentbild
ON equipment.bild_id = equipmentbild.bild_id 
WHERE equipment.geloescht=false
AND equipment.set_id IS NULL
AND equipment.indispo=true
ORDER BY name DESC;


--sets alleine
SELECT 
set_.set_id, 
set_.name,
set_.beschrieb,
equipmentbild.filename,
kategorie.name AS kat_name,
set_.aktiv
FROM set_ LEFT JOIN kategorie 
ON set_.kategorie_id = kategorie.kategorie_id LEFT JOIN equipmentbild
ON set_.bild_id = equipmentbild.bild_id 
WHERE set_.geloescht=false
ORDER BY name DESC;

--bookings
SELECT 
equipment.equipment_id AS eq_id,
equipment.name AS eq_name,
set_.set_id AS set_id,
set_.name AS set_name
FROM buchung 
LEFT JOIN equipment ON buchung.equipment_id = equipment.equipment_id
LEFT JOIN set_ ON equipment.set_id = set_.set_id
WHERE reserviert_fuer = ? AND storniert=false;