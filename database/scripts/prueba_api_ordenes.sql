-- =====================================================================
-- PRUEBA API DE ORDENES DE PAGO
-- Crea 2 tramites y 2 ordenes de pago (pendientes, sin folio)
-- + los registros minimos (usuario y solicitudes) para que la API
-- devuelva el nombre del ciudadano.
--
-- SEGURO: usa IDs propios (901, 902), re-ejecutable (borra SOLO sus
-- registros) y no altera datos reales.
-- EJECUTAR unicamente en la base de PRUEBAS, no en produccion.
-- =====================================================================

SET NAMES utf8mb4;
START TRANSACTION;

-- 1) Limpiar intentos anteriores (por si ya se ejecuto)
DELETE FROM `ordenes_pagos` WHERE `id_orden_pago` IN (901, 902);
DELETE FROM `tbl_solicitudes` WHERE `id_solicitud` IN (901, 902);
DELETE FROM `cat_tramites`    WHERE `id_tramite` IN (901, 902);
DELETE FROM `users`           WHERE `email` = 'api.prueba@test.local';

-- 2) Usuario ciudadano de prueba (necesario para "nombre_ciudadano")
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `bloqueado`, `password`, `created_at`, `updated_at`)
VALUES (901, 'Ciudadano Prueba API', 'api.prueba@test.local', NOW(), 0,
        '$2y$12$XvZyTm4MK2h.oio7WAoaK.WFgVLzVLp93SIzeIcqUePPteMCPBrJC', NOW(), NOW());

-- 3) Dos tramites de prueba (referencian la dependencia existente id=1)
INSERT INTO `cat_tramites`
(`id_tramite`, `nombre_tramite`, `descripcion_tramite`, `estatus_tramite`, `fk_dependencia`, `precio_tramite`, `tramite_cri`, `cobra_por_m2`, `cuenta_predial`, `created_at`, `updated_at`)
VALUES
(901, 'PRUEBA API - Tramite Uno', 'Tramite de prueba para la API de ordenes de pago', 1, 1, 150.00, 111, 0, 1, NOW(), NOW()),
(902, 'PRUEBA API - Tramite Dos', 'Tramite de prueba para la API de ordenes de pago', 1, 1, 250.00, 222, 0, 1, NOW(), NOW());

-- 4) Dos solicitudes de prueba (usuario + tramite)
INSERT INTO `tbl_solicitudes`
(`id_solicitud`, `fk_usuario`, `fk_tramite`, `fk_predio`, `fecha_solicitud`, `estatus_solicitud`, `created_at`, `updated_at`)
VALUES
(901, 901, 901, NULL, NOW(), 3, NOW(), NOW()),
(902, 901, 902, NULL, NOW(), 3, NOW(), NOW());

-- 5) Dos ordenes de pago (Pendiente=1, sin folio) -> saldran en el endpoint 1
INSERT INTO `ordenes_pagos`
(`id_orden_pago`, `nombre_tramite`, `precio_tramite`, `numero_cri`, `orden_estatus`, `folio_pago`, `fk_tramite`, `fk_solicitud`, `created_at`, `updated_at`)
VALUES
(901, 'PRUEBA API - Tramite Uno', 150.00, 111, 1, NULL, 901, 901, NOW(), NOW()),
(902, 'PRUEBA API - Tramite Dos', 250.00, 222, 1, NULL, 902, 902, NOW(), NOW());

COMMIT;

-- 6) Verificacion: asi las vera la API
SELECT op.id_orden_pago,
       op.nombre_tramite,
       op.precio_tramite,
       op.numero_cri,
       op.orden_estatus,
       op.folio_pago,
       u.name                AS ciudadano,
       d.nombre_dependencia  AS dependencia,
       t.tramite_cri         AS cri_tramite
FROM `ordenes_pagos` op
JOIN `cat_tramites` t      ON t.id_tramite = op.fk_tramite
JOIN `tbl_solicitudes` s   ON s.id_solicitud = op.fk_solicitud
JOIN `users` u             ON u.id = s.fk_usuario
JOIN `cat_dependencias` d  ON d.id_dependencia = t.fk_dependencia
WHERE op.id_orden_pago IN (901, 902);
