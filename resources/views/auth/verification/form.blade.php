<!-- resources/views/verify.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>Verify Phone Number</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <div class="max-w-md mx-auto bg-white p-5 rounded-md shadow-sm">
            <h2 class="text-2xl font-bold mb-5">Verify Your Phone Number</h2>
            @if (session('message'))
                <div class="bg-red-500 text-white p-2 mb-4 rounded">
                    {{ session('message') }}
                </div>
            @endif
            <form method="POST" action="{{ route('verification.verify') }}">
                @csrf
                <div class="mb-4">
                    <label for="phone_number" class="block text-gray-700">Phone Number</label>
                    <input type="text" name="phone_number" id="phone_number" value="{{ request('phone_number') }}" readonly class="w-full p-2 border border-gray-300 rounded mt-1">
                </div>
                <div class="mb-4">
                    <label for="verification_code" class="block text-gray-700">Verification Code</label>
                    <input type="text" name="verification_code" id="verification_code" required class="w-full p-2 border border-gray-300 rounded mt-1">
                </div>
                <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Verify</button>
            </form>
        </div>
    </div>
</body>
</html>
