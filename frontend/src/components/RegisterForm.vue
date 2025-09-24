<template>
  <div class="auth-container">
    <div class="auth-box">
      <h2>Register</h2>

      <Form
        ref="formRef"
        @submit="handleSubmit"
        :validation-schema="schema"
      >
        <div class="form-group">
          <Icon icon="mdi-light:account" class="icon" width="24" height="24" />
          <label for="full_name">Full Name</label>
          <Field 
            id="full_name" 
            name="full_name" 
            type="text" 
            placeholder="Full Name" 
            style="padding-left: 2.5rem;" 
            class="rounded py-2 w-full placeholder:text-sm" 
          />
          <ErrorMessage name="full_name" class="error" />
        </div>

        <div class="form-group">
          <Icon icon="mdi-light:email" class="icon" width="24" height="24" />
          <label for="email">Email</label>
          <Field 
            id="email" 
            name="email" 
            type="email" 
            placeholder="Email" 
            style="padding-left: 2.5rem;" 
            class="rounded py-2 w-full placeholder:text-sm" 
          />
          <ErrorMessage name="email" class="error" />
        </div>

        <div class="form-group">
          <Icon icon="mdi-light:phone" class="icon" width="24" height="24" />
          <label for="phone">Phone</label>
          <Field 
            id="phone" 
            name="phone" 
            type="text" 
            placeholder="Phone" 
            style="padding-left: 2.5rem;" 
            class="rounded py-2 w-full placeholder:text-sm" 
          />
          <ErrorMessage name="phone" class="error" />
        </div>

        <div class="form-group">
          <Icon icon="mdi-light:home" class="icon" width="24" height="24" />
          <label for="address">Address</label>
          <Field 
            id="address" 
            name="address" 
            type="text" 
            placeholder="Address" 
            style="padding-left: 2.5rem;" 
            class="rounded py-2 w-full placeholder:text-sm" 
          />
          <ErrorMessage name="address" class="error" />
        </div>

        <div class="form-group">
          <Icon icon="mdi-light:lock" class="icon" width="24" height="24" />
          <label for="password">Password</label>
          <Field 
            id="password" 
            name="password" 
            :type="showPassword ? 'text' : 'password'" 
            placeholder="Password" 
            style="padding-left: 2.5rem;" 
            class="rounded py-2 w-full placeholder:text-sm" 
          />
          <button @click="togglePassword" @mousedown.prevent type="button" class="icon-eye">
            <Icon :icon="showPassword ? 'mdi-light:eye-off' : 'mdi-light:eye'" width="24" height="24" />
          </button>
          <ErrorMessage name="password" class="error" />
        </div>

        <div class="form-group">
          <Icon icon="mdi-light:lock" class="icon" width="24" height="24" />
          <label for="password_confirmation">Confirm Password</label>
          <Field 
            id="password_confirmation" 
            name="password_confirmation" 
            :type="showConfirmPassword ? 'text' : 'password'" 
            placeholder="Confirm Password" 
            style="padding-left: 2.5rem;" 
            class="rounded py-2 w-full placeholder:text-sm" 
          />
          <button @click="toggleConfirmPassword" type="button" class="icon-eye">
            <Icon :icon="showConfirmPassword ? 'mdi-light:eye-off' : 'mdi-light:eye'" width="24" height="24" />
          </button>
          <ErrorMessage name="password_confirmation" class="error" />
        </div>

        <button type="submit" class="btn">
          Register
        </button>
      </Form>

      <p class="toggle" @click="$emit(router.push('/auth/login'))">
        Already have an account? Login
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, nextTick } from "vue"
import { Form, Field, ErrorMessage } from "vee-validate"
import * as yup from "yup"
import axios from "axios" 
import { Icon } from '@iconify/vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const formRef = ref(null) 
const showPassword = ref(false)
const showConfirmPassword = ref(false)

const schema = yup.object({
  full_name: yup.string().required("Full name is required"),
  phone: yup.string()
    .matches(/^\+?[1-9]\d{1,14}$/, "Invalid phone number")
    .required("Phone number is required"),
  address: yup.string().required("Address is required"),
  email: yup.string().email("Invalid email").required("Email is required"),
  password: yup.string().min(6, "At least 6 characters").required("Password is required"),
  password_confirmation: yup.string()
    .oneOf([yup.ref("password")], "Passwords must match")
    .required("Confirm password is required")
})

function togglePassword(event) {
  event.preventDefault()
  event.stopPropagation()
  showPassword.value = !showPassword.value
  
  nextTick(() => {
    const passwordField = document.getElementById('password')
    if (passwordField) {
      passwordField.focus()
    }
  })
}

function toggleConfirmPassword(event) {
  event.preventDefault()
  event.stopPropagation()
  showConfirmPassword.value = !showConfirmPassword.value
  
  nextTick(() => {
    const confirmPasswordField = document.getElementById('password_confirmation')
    if (confirmPasswordField) {
      confirmPasswordField.focus()
    }
  })
}

async function handleSubmit(values) {
  try {
    const response = await axios.post("http://127.0.0.1:8000/api/auth/register", {
      full_name: values.full_name,
      email: values.email,
      phone: values.phone,
      address: values.address,
      password: values.password,
      password_confirmation: values.password_confirmation
    })

    alert("Registration successful! Please login.")
    router.push('/auth/login')
    nextTick(() => {
      formRef.value?.resetForm()
    })
  } catch (error) {
    console.error("Registration failed:", error)
    alert(error.response?.data?.message || "Registration failed. Please try again.")
  }
}
</script>

<style src="./AuthForm.css"></style>