<template>
    <div class="min-h-screen flex flex-col bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
        <AdminHeader />
        <div class="flex flex-1">
            <AdminSidebar />
            <main class="flex-1 p-10">
                <h1 class="text-2xl font-bold mb-6 text-white">사용자 관리</h1>
                <table class="w-full bg-white/10 rounded-xl border border-white/20">
                    <thead>
                        <tr class="bg-white/20 text-white">
                            <th class="py-3 px-4 text-left">ID</th>
                            <th class="py-3 px-4 text-left">이름</th>
                            <th class="py-3 px-4 text-left">이메일</th>
                            <th class="py-3 px-4 text-left">어드민</th>
                            <th class="py-3 px-4 text-left">가입일</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id" class="border-t border-white/10 hover:bg-white/5 text-white/90">
                            <td class="py-2 px-4">{{ user.id }}</td>
                            <td class="py-2 px-4">{{ user.name }}</td>
                            <td class="py-2 px-4">{{ user.email }}</td>
                            <td class="py-2 px-4">
                                <span v-if="user.is_admin" class="text-green-400 font-bold">어드민</span>
                                <span v-else class="text-gray-300">일반</span>
                            </td>
                            <td class="py-2 px-4">{{ user.created_at ? user.created_at.substring(0, 10) : '' }}</td>
                        </tr>
                    </tbody>
                </table>
            </main>
        </div>
    </div>
</template>

<script setup>
import AdminHeader from '@/Components/AdminHeader.vue';
import AdminSidebar from '@/Components/AdminSidebar.vue';
const props = defineProps({
    users: Array
});
</script> 