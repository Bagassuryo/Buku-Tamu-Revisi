<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

    @include('layouts.nav')    
    
    <main class="flex-1">
        
        
        <div class="h-[calc(100vh-128px)] flex flex-col items-center justify-center px-4">
            
            <!-- Heading -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">
                    Login Administrator
                </h1>
            </div>
            
            <div class="border border-gray-300 rounded-2xl shadow-lg p-8 max-w-xl w-full">
                
                @if($errors->has('login_error'))
                    <div class="bg-red-500 text-white p-4 rounded-xl mb-5 shadow-lg">
                        {{ $errors->first('login_error') }}
                    </div>
                @endif
                
                <!-- Form -->
                <form action="/login" method="POST" autocomplete="off">
                    @csrf
                    
                    <!-- Username -->
                    <div class="mb-5">
                        <label for="username"
                        class="block text-sm font-semibold text-black mb-2">
                        Username
                    </label>
                    
                    <input
                    type="text"
                    id="username"
                    name="username"
                    autocomplete="off"
                    required
                    class="w-full px-4 py-3 border border-black rounded-xl
                    focus:outline-none focus:ring-2 focus:ring-blue-400
                    focus:border-blue-400 transition duration-200"
                    placeholder="Masukkan username">
                </div>
                
                <!-- Password -->
                <div class="mb-6">
                    <label for="password"
                    class="block text-sm font-semibold text-black mb-2">
                    Password
                </label>
                
                <input
                type="password"
                id="password"
                name="password"
                autocomplete="off"
                required
                class="w-full px-4 py-3 border border-black rounded-xl
                focus:outline-none focus:ring-2 focus:ring-blue-400
                focus:border-blue-400 transition duration-200"
                placeholder="Masukkan password">
            </div>

            <script src="https://www.google.com/recaptcha/api.js" async defer></script>

            <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="g-recaptcha mb-4" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                
            <!-- Button -->
                <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700
                text-white font-semibold py-3 rounded-xl cursor-pointer
                transition duration-300 shadow-md hover:shadow-lg">
                
                Login
            </button>
            
        </form>
    </div>
    
</div>

</main>
@include('layouts.footer')
</body>
</html>