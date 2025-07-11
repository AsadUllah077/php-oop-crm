<?php

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            overflow-x: hidden;
        }

        .sidebar {
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding-top: 20px;
            background-color: #343a40;
            color: white;
            transition: all 0.3s;
            z-index: 1000;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 5px;
            border-radius: 5px;
            padding: 10px 15px;
        }

        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link.active {
            color: white;
            background-color: #007bff;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
        }

        .main-content {
            margin-left: 250px;
            width: 100%;
            padding: 20px;
            transition: all 0.3s;
        }

        .sidebar-collapsed .sidebar {
            width: 80px;
        }

        .sidebar-collapsed .sidebar .nav-link span {
            display: none;
        }

        .sidebar-collapsed .sidebar .nav-link {
            text-align: center;
            padding: 10px 5px;
        }

        .sidebar-collapsed .sidebar .nav-link i {
            margin-right: 0;
            font-size: 1.2rem;
        }

        .sidebar-collapsed .main-content {
            margin-left: 80px;
        }

        .sidebar-header {
            padding: 10px 15px;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .toggle-btn {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .category-dropdown .dropdown-menu {
            position: static !important;
            transform: none !important;
            background-color: #2c3136;
            border: none;
            margin: 0;
            padding: 0;
        }

        .category-dropdown .dropdown-item {
            color: rgba(255, 255, 255, 0.8);
            padding: 8px 15px 8px 30px;
        }

        .category-dropdown .dropdown-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
    </style>
</head>

<body>