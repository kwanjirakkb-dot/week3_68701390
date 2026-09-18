<template>
  <div class="container mt-4 col-md-4 bg-body-secondary">
    <h2 class="text-center mb-3">เพิ่มข้อมูลการติดต่อ</h2>

    <form @submit.prevent="addData">

      <!-- หัวข้อ -->
      <div class="mb-2">
        <input
          v-model="contact.subject"
          class="form-control"
          placeholder="หัวข้อ"
          required
        />
      </div>

      <!-- รายละเอียด -->
      <div class="mb-2">
        <textarea
          v-model="contact.detail"
          class="form-control"
          placeholder="รายละเอียด"
          rows="4"
          required
        ></textarea>
      </div>

      <!-- ชื่อ-นามสกุล -->
      <div class="mb-2">
        <input
          v-model="contact.fullname"
          class="form-control"
          placeholder="ชื่อ-นามสกุล"
          required
        />
      </div>

      <!-- Email -->
      <div class="mb-2">
        <input
          type="email"
          v-model="contact.email"
          class="form-control"
          placeholder="Email"
          required
        />
      </div>

      <!-- ปุ่ม -->
      <div class="text-center mt-4">
        <button
          type="submit"
          class="btn btn-primary mb-4"
        >
          บันทึก
        </button>

        &nbsp;

        <button
          type="reset"
          class="btn btn-secondary mb-4"
        >
          ยกเลิก
        </button>
      </div>

    </form>

    <!-- Message -->
    <div
      v-if="message"
      class="alert alert-info mt-3"
    >
      {{ message }}
    </div>

  </div>
</template>


<script>
export default {
  data() {
    return {
      contact: {
        subject: "",
        detail: "",
        fullname: "",
        email: ""
      },

      message: ""
    };
  },

  methods: {

    async addData() {
      try {

        // สร้าง FormData
        const formData = new FormData();

        formData.append("subject", this.contact.subject);
        formData.append("detail", this.contact.detail);
        formData.append("fullname", this.contact.fullname);
        formData.append("email", this.contact.email);

        const res = await fetch(
          "http://localhost/week3_68701390/php_api/add_contact.php",
          {
            method: "POST",
            body: formData
          }
        );

        if (!res.ok) {
          throw new Error("ไม่สามารถบันทึกข้อมูลได้");
        }

        const data = await res.json();

        this.message = data.message;

        if (data.success) {

          // เคลียร์ข้อมูลหลังบันทึกสำเร็จ
          this.contact = {
            subject: "",
            detail: "",
            fullname: "",
            email: ""
          };

        }

      } catch (err) {

        this.message =
          "เกิดข้อผิดพลาด: " + err.message;

      }
    }

  }
};
</script>

