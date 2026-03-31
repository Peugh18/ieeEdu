<template>
  <div class="min-h-screen bg-gray-50">
    <div class="flex">
      <aside class="w-64 bg-white border-r border-gray-200 min-h-screen p-4">
        <div class="mb-8 font-bold text-xl">Admin Panel</div>
        <nav class="space-y-2">
          <router-link :href="route('admin.dashboard')" class="block px-3 py-2 rounded-md hover:bg-gray-100">Dashboard</router-link>
          <router-link :href="route('admin.users.index')" class="block px-3 py-2 rounded-md hover:bg-gray-100">Usuarios</router-link>
          <router-link :href="route('admin.courses.index')" class="block px-3 py-2 rounded-md hover:bg-gray-100">Cursos</router-link>
          <router-link :href="route('admin.categories.index')" class="block px-3 py-2 rounded-md hover:bg-gray-100">Categorías</router-link>
          <router-link :href="route('admin.payments.index')" class="block px-3 py-2 rounded-md hover:bg-gray-100">Pagos</router-link>
        </nav>
      </aside>
      <main class="flex-1 p-6">
        <slot />
      </main>
    </div>
  </div>
</template>
