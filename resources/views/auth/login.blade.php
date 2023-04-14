<x-layout>
    <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <img class="mx-auto h-12 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=sky&shade=600"
                alt="Your Company">
            <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">
                {{ config('app.name') ?? 'App' }}</h2>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white px-4 py-8 shadow sm:rounded-lg sm:px-10">
                @error('username')
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ $message }}</span>
                    </div>
                @enderror
                <form class="space-y-6" action="#" method="POST">
                    @csrf
                    <div>
                        <label for="username" class="block text-sm font-medium leading-6 text-gray-900">Username
                            (GUID)</label>
                        <div class="mt-2">
                            <input id="username" name="username" type="username" autocomplete="username" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-sky-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                        <div class="mt-2">
                            <input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-sky-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember-me" name="remember-me" type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-sky-600 focus:ring-sky-600">
                            <label for="remember-me" class="ml-2 block text-sm text-gray-900">Remember me on this
                                device</label>
                        </div>

                    </div>

                    <div>
                        <button type="submit"
                            class="flex w-full justify-center rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">Sign
                            in</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-layout>
