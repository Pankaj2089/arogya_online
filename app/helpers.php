<?php

if (!function_exists('admin_dept_id')) {
    /**
     * When logged-in user is Doctor or Operator, returns their department id for data scoping.
     * Returns null for Admin/Account (no filter).
     *
     * @return int|null
     */
    function admin_dept_id()
    {
        $type = session('admin_type');
        if ($type === 'Doctor' || $type === 'Operator') {
            $deptId = session('admin_dept_id');
            return $deptId ? (int) $deptId : null;
        }
        return null;
    }
}
