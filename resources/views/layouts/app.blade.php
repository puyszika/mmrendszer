<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Admin – CS2 Kontroller</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-gray-800 text-white p-4 space-y-4">
            <h1 class="text-2xl font-bold mb-6">Admin Panel</h1>
            <nav class="space-y-2">
                <a href="{{ route('admin.servers.index') }}" class="block hover:bg-gray-700 px-3 py-2 rounded">Szerverek</a>
                <!-- Bővíthető extra menüpontokkal -->
            </nav>
        </aside>
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>
</body>
</html>
