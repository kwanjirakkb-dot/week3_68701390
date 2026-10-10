<template>
  <div class="container mt-4">
    <h2 class="mb-3">ข้อมูลการติดต่อ</h2>
    
    <div class="mb-3">
      <button class="btn btn-primary" @click="openAddModal">
        Add <i class="bi bi-plus-circle"></i>
      </button>
    </div>

    <table class="table table-bordered table-striped">
      <thead class="table-primary">
        <tr>
          <th>รหัสติดต่อ</th>
          <th>หัวข้อ</th>
          <th>รายละเอียด</th>
          <th>ชื่อ-นามสกุล</th>
          <th>Email</th>
          <th>วันที่เพิ่มข้อมูล</th>
          <th>แก้ไข/ลบ</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in contacts" :key="contacts.id">
          <td>{{ item.id }}</td>
          <td>{{ item.subject  }}</td>
          <td>{{ item.detail }}</td>
          <td>{{ item.fullname }}</td>
          <td>{{ item.email }}</td>
          <td>{{ item.created_at }}</td>
        <td>
            
            <button class="btn btn-warning btn-sm" @click="openEditModal(item)">
              แก้ไข
            </button>
            |
            <button class="btn btn-danger btn-sm" @click="deleteCustomer(item.id)">
              ลบ
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="loading" class="text-center"><p>กำลังโหลดข้อมูล...</p></div>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <!-- ✅ Modal ใช้ทั้งเพิ่ม/แก้ไข -->
    <div class="modal fade" id="editModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEditMode ? "แก้ไขข้อมูล" : "เพิ่มข้อมูลใหม่" }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveCustomer">
              <div class="mb-3">
                <label class="form-label">หัวข้อ</label>
                <input v-model="editCustomer.subject" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">รายละเอียด</label>
                <input v-model="editCustomer.detail" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">ชื่อ-นามสกุล</label>
                <input v-model="editCustomer.fullname" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input v-model="editCustomer.email" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">วันที่เพิ่มข้อมูล</label>
                <input v-model="editCustomer.created_at" type="text" class="form-control" required>
              </div>

              <button type="submit" class="btn btn-success">
                {{ isEditMode ? "บันทึกการแก้ไข" : "เพิ่มข้อมูล" }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import { ref, onMounted } from "vue";

export default {
  name: "CustomerList",
  setup() {
    const contacts = ref([]);
    const loading = ref(true);
    const error = ref(null);
    const editCustomer = ref({});
    const isEditMode = ref(false);
    let editModal = null;

    const fetchCustomers = async () => {
      try {
        const response = await fetch("http://localhost/week3_68701390/php_api/contact_crud.php");
        const result = await response.json();

    if (!response.ok || !result.success) {
      throw new Error(
        result.message || "ไม่สามารถดึงข้อมูลได้"
      );
    }

    contacts.value = result.data;
    error.value = null;

  } catch (err) {
    error.value = err.message;

  } finally {
    loading.value = false;
  }
};

    onMounted(() => {
      fetchCustomers();
      const modalEl = document.getElementById("editModal");
      editModal = new window.bootstrap.Modal(modalEl);
    });

    // ✅ เปิด Modal เพิ่มลูกค้าใหม่
    const openAddModal = () => {
      isEditMode.value = false;
      editCustomer.value = {

        subject: "",
        detail: "",
        fullname: "",
        email: "",

      };
      editModal.show();
    };

    // ✅ เปิด Modal แก้ไขลูกค้า
    const openEditModal = (item) => {
      isEditMode.value = true;
      editCustomer.value = { ...item };
      editModal.show();
    };

    // ✅ ใช้ฟังก์ชันเดียวสำหรับทั้งเพิ่ม/แก้ไข
    const saveCustomer = async () => {
      const url = "http://localhost/week3_68701390/php_api/contact_crud.php";
      const method = isEditMode.value ? "PUT" : "POST";

      try {
        const response = await fetch(url, {
          method,
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(editCustomer.value)
        });

        const result = await response.json();

        if (result.success) {
          alert(result.message);
          fetchCustomers();
          editModal.hide();
        } else {
          alert(result.message);
        }
      } catch (err) {
        alert("เกิดข้อผิดพลาด: " + err.message);
      }
    };

    // ✅ ลบลูกค้า
    const deleteCustomer = async (id) => {
      if (!confirm("คุณต้องการลบข้อมูลนี้ใช่หรือไม่?")) return;
      try {
        const response = await fetch("http://localhost/week3_68701390/php_api/contact_crud.php", {
          method: "DELETE",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id: id })
        });
        const result = await response.json();
        if (result.success) {
          contacts.value = contacts.value.filter(c => c.id !== id);
          alert(result.message);
        } else {
          alert(result.message);
        }
      } catch (err) {
        alert("เกิดข้อผิดพลาด: " + err.message);
      }
    };

    return {
      contacts,
      loading,
      error,
      editCustomer,
      isEditMode,
      openAddModal,
      openEditModal,
      saveCustomer,
      deleteCustomer
    };
  }
};
</script>
