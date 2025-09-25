<template>
  <div class="auth-container">
    <div class="auth-box">
      <h2>Login</h2>

      <Form
        ref="formRef"
        @submit="handleSubmit"
        :validation-schema="schema"
      >
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

        <button type="submit" class="btn">
          Login
        </button>
      </Form>

      <p class="toggle" @click="$emit(router.push('/auth/register'))">
        Don't have an account? Register
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
import { useRouter } from "vue-router" 
import api from "@/api"

const router = useRouter()
const formRef = ref(null) 
const showPassword = ref(false)

const schema = yup.object({
  email: yup.string().email("Invalid email").required("Email is required"),
  password: yup.string().min(6, "At least 6 characters").required("Password is required")
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

async function handleSubmit(values) {
  try {
    const response = await api.post("/auth/login", {
      email: values.email,
      password: values.password
    })
    if (response.status !== 200) {
      throw new Error("Login failed")
    }
    localStorage.setItem("token", response.data.access_token)
    localStorage.setItem("expires_in", response.data.expires_in)
    router.push('/todoPage')
    alert("Login successful!")

  } catch (error) {
    console.error("Login failed:", error)
    alert(error.response?.data?.message || "Login failed. Please check your credentials.")
  }
}
</script>

<style src="./AuthForm.css"></style>