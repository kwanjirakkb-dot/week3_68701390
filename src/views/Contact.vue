<template>
  <div class="container mt-4">
    <!-- หัวข้อหน้า -->
    <h2 class="mb-3">ข้อมูลการติดต่อ</h2>

    <!-- ปุ่มเพิ่มข้อมูล -->
    <div class="text-end mb-3">
      <a href="/add_contact" class="btn btn-info">Add+</a>
    </div>

    <!-- ตาราง -->
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ลำดับที่</th>
          <th>รหัสติดต่อ</th>
          <th>หัวข้อ</th>
          <th>รายละเอียด</th>
          <th>ชื่อ-นามสกุล</th>
          <th>Email</th>
          <th>วันที่เพิ่มข้อมูล</th>
        </tr>
      </thead>

      <tbody>
        <tr
          v-for="(item, index) in contacts"
          :key="item.id"
        >
          <td>{{ index + 1 }}</td>
          <td>{{ item.id }}</td>
          <td>{{ item.subject }}</td>
          <td>{{ item.detail }}</td>
          <td>{{ item.fullname }}</td>
          <td>{{ item.email }}</td>
          <td>{{ item.created_at }}</td>
        </tr>
      </tbody>
    </table>

    <!-- Loading -->
    <div v-if="loading" class="text-center">
      <p>กำลังโหลดข้อมูล...</p>
    </div>

    <!-- Error -->
    <div v-if="error" class="alert alert-danger">
      {{ error }}
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";

export default {
  name: "ContactList",

  setup() {
    const contacts = ref([]);
    const loading = ref(true);
    const error = ref(null);

    const fetchdata = async () => {
      try {
        const response = await fetch(
          "http://localhost/week3_68701390/php_api/contacts.php"
        );

        if (!response.ok) {
          throw new Error("ไม่สามารถดึงข้อมูลได้");
        }

        contacts.value = await response.json();

      } catch (err) {
        error.value = err.message;

      } finally {
        loading.value = false;
      }
    };

    onMounted(() => {
      fetchdata();
    });

    return {
      contacts,
      loading,
      error
    };
  }
};
</script>

