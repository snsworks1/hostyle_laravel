<template>
    <aside class="w-56 min-h-screen bg-gradient-to-b from-purple-900 to-blue-900 border-r border-white/20 py-8 px-4">
        <nav class="space-y-2">
            <a href="/admin/users" class="block px-4 py-3 rounded-lg text-white/80 hover:bg-white/10 hover:text-white font-semibold">사용자 관리</a>
            <a href="/admin/user-servers" class="block px-4 py-3 rounded-lg text-white/80 hover:bg-white/10 hover:text-white font-semibold">사용자 서버 관리</a>
            <a href="/admin/cyberpanel" class="block px-4 py-3 rounded-lg text-white/80 hover:bg-white/10 hover:text-white font-semibold">사이버패널 연동</a>
            <a href="/admin/plans" class="block px-4 py-3 rounded-lg text-white/80 hover:bg-white/10 hover:text-white font-semibold">플랜 관리</a>
        </nav>
    </aside>
</template>

<script setup>
// 추후 props로 메뉴 확장 가능
</script> 