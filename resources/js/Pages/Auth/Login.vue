<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">



            <!-- <div class="max-w-md mx-auto bg-white shadow-md rounded-lg p-6"> -->
                <div class="text-center mb-6 text-xl font-light text-gray-600 sm:text-2xl dark:text-white">Log in to the store</div>
                <!-- Email Field -->
                <div class="mb-">
                    <!-- <label for="email" class="block text-sm font-medium text-gray-700">Email</label> -->
                    <div class="flex relative items-center border border-gray-300 rounded-md overflow-hidden">
                        <span class="inline-flex items-center px-3 bg-white text-gray-500">
                            <svg width="15" height="15" fill="currentColor" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1792 710v794q0 66-47 113t-113 47h-1472q-66 0-113-47t-47-113v-794q44 49 101 87 362 246 497 345 57 42 92.5 65.5t94.5 48 110 24.5h2q51 0 110-24.5t94.5-48 92.5-65.5q170-123 498-345 57-39 100-87zm0-294q0 79-49 151t-122 123q-376 261-468 325-10 7-42.5 30.5t-54 38-52 32.5-57.5 27-50 9h-2q-23 0-50-9t-57.5-27-52-32.5-54-38-42.5-30.5q-91-64-262-182.5t-205-142.5q-62-42-117-115.5t-55-136.5q0-78 41.5-130t118.5-52h1472q65 0 112.5 47t47.5 113z">
                                </path>
                            </svg>
                        </span>
                        <input
                            id="email"
                            type="email"
                            class="block w-full border-0 focus:ring-0 focus:outline-none px-3 py-2"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username" placeholder="Email"
                        />
                        
                    </div>
                    <InputError class="mt-2" :message="form.errors.email" />

                </div>

                <!-- Password Field -->
                <div class="mb-4 mt-2">
                    <!-- <label for="password" class="block text-sm font-medium text-gray-700">Password</label> -->
                    <div class="flex relative items-center border border-gray-300 rounded-md overflow-hidden">
                        <span class="inline-flex items-center px-3 bg-white text-gray-500">
                            <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2a6 6 0 00-6 6v4H5a3 3 0 00-3 3v5a3 3 0 003 3h14a3 3 0 003-3v-5a3 3 0 00-3-3h-1V8a6 6 0 00-6-6zm-4 6a4 4 0 018 0v4H8V8zm10 6a1 1 0 011 1v5a1 1 0 01-1 1H6a1 1 0 01-1-1v-5a1 1 0 011-1h12z"></path>
                            </svg>
                        </span>
                        <input
                            id="password"
                            type="password"
                            class="block w-full border-0 focus:ring-0 focus:outline-none px-3 py-2"
                            v-model="form.password"
                            required
                            autocomplete="current-password" placeholder="Password"
                        />
                    </div>
                </div>
                <InputError class="mt-2" :message="form.errors.password" />

                <!-- Remember Me Checkbox -->
                <div class="flex items-center mb-4">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                </div>

                <!-- Forgot Password Link and Login Button -->
                <div class="flex flex-col items-center">
                    <button class="w-full bg-purple-600 text-white py-2 rounded-md mb-2 hover:bg-gray-800 transition-colors">LOG IN</button>
                    <Link
                    v-if="canResetPassword"
                    :href="route('register')"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    <!-- Forgot your password? -->
                     Register
                </Link>
                </div>
        </form>
    </GuestLayout>
</template>
