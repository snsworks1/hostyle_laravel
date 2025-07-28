<template>
    <header class="bg-gradient-to-r from-purple-800 to-blue-800 px-8 py-4 flex items-center justify-between shadow">
        <div class="flex items-center space-x-4">
            <span class="text-2xl font-bold text-white tracking-widest">ADMIN</span>
            <nav class="flex items-center space-x-6 ml-8">
                <a href="/admin/users" class="text-white/80 hover:text-white font-semibold">사용자 관리</a>
                <a href="/admin/user-servers" class="text-white/80 hover:text-white font-semibold">사용자 서버 관리</a>
                <!-- 추후 다른 관리자 메뉴 추가 가능 -->
            </nav>
        </div>
        <div>
            <form method="POST" :action="route('logout')">
                <button type="submit" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg font-semibold transition">로그아웃</button>
            </form>
        </div>
    </header>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
</script> 