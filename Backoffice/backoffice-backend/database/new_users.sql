-- =====================================================
-- NEW USERS FOR TESTING ROLES
-- =====================================================
-- Password untuk semua user: test123
-- (Password sudah di-hash dengan salt)
-- =====================================================

-- User Manager (role_id = 2)
INSERT INTO master.mst_users (email, "password", display_name, created_date, status, last_login, salt, role_id, branch_id, phone, profile_picture) VALUES
    ('manager@test.com', 'Xq5bO1QE8aJDxPc2IluWQzLqIfIjIhQh/KZCz5mEE5c=', 'Test Manager', NOW(), 'Active', NULL, 'pGEjZJXFcH5I3LoPjCHOac6N9Ly7PqoVI2qeXBsC1nM=', 2, 1, NULL, NULL);

-- User Supervisor (role_id = 3)
INSERT INTO master.mst_users (email, "password", display_name, created_date, status, last_login, salt, role_id, branch_id, phone, profile_picture) VALUES
    ('supervisor@test.com', 'Xq5bO1QE8aJDxPc2IluWQzLqIfIjIhQh/KZCz5mEE5c=', 'Test Supervisor', NOW(), 'Active', NULL, 'pGEjZJXFcH5I3LoPjCHOac6N9Ly7PqoVI2qeXBsC1nM=', 3, 1, NULL, NULL);

-- User Staff (role_id = 4)
INSERT INTO master.mst_users (email, "password", display_name, created_date, status, last_login, salt, role_id, branch_id, phone, profile_picture) VALUES
    ('staff@test.com', 'Xq5bO1QE8aJDxPc2IluWQzLqIfIjIhQh/KZCz5mEE5c=', 'Test Staff', NOW(), 'Active', NULL, 'pGEjZJXFcH5I3LoPjCHOac6N9Ly7PqoVI2qeXBsC1nM=', 4, 1, NULL, NULL);

-- User Staff 2 (role_id = 4) - untuk testing tambahan
INSERT INTO master.mst_users (email, "password", display_name, created_date, status, last_login, salt, role_id, branch_id, phone, profile_picture) VALUES
    ('staff2@test.com', 'Xq5bO1QE8aJDxPc2IluWQzLqIfIjIhQh/KZCz5mEE5c=', 'Test Staff 2', NOW(), 'Active', NULL, 'pGEjZJXFcH5I3LoPjCHOac6N9Ly7PqoVI2qeXBsC1nM=', 4, 1, NULL, NULL);

-- =====================================================
-- SUMMARY USER CREDENTIALS FOR TESTING:
-- =====================================================
-- | Email                | Password | Role        |
-- |---------------------|----------|-------------|
-- | manager@test.com    | test123  | Manager     |
-- | supervisor@test.com | test123  | Supervisor  |
-- | staff@test.com      | test123  | Staff       |
-- | staff2@test.com     | test123  | Staff       |
-- =====================================================
