-- =====================================================
-- TAO TAI KHOAN ADMIN MOI
-- Login:    admin
-- Password: admin123
-- Level:    1 (Admin cao nhat)
-- =====================================================

INSERT INTO `users` (
  `fullname`,
  `username`,
  `email`,
  `reset_link_token`,
  `exp_date`,
  `level`,
  `saldo`,
  `status`,
  `uplink`,
  `password`,
  `telegram_id`,
  `seller_key`,
  `user_ip`,
  `created_at`,
  `updated_at`,
  `expiration_date`
) VALUES (
  'HOANG',
  'admin',
  'admin@hclou.local',
  '',
  '2050-01-01 00:00:00',
  1,
  9999999.9,
  1,
  'SX2TEAM',
  '$2y$08$kOhvFKwvt1VvRvo7lKhVO.FHcNH2edKLMXC9HD3TzzBd4yz7AWwqq',
  NULL,
  'seller_HCLOU2026admin0001abcdef0001abcd',
  '127.0.0.1',
  NOW(),
  NOW(),
  '2050-01-01 00:00:00'
);

-- =====================================================
-- NEU BAO LOI "Duplicate entry 'admin' for username"
-- => Xoa tai khoan admin cu truoc (uncomment dong duoi):
-- =====================================================
-- DELETE FROM `users` WHERE `username` = 'admin';

-- =====================================================
-- KIEM TRA: Tai khoan admin moi tao
-- =====================================================
-- SELECT id_users, username, level, saldo FROM `users` WHERE username='admin';
