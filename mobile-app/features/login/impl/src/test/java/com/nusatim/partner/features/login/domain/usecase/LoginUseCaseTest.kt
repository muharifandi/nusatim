package com.nusatim.partner.features.login.domain.usecase

import app.cash.turbine.test
import com.nusatim.partner.core.domain.repository.AuthRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.LoginResponse
import io.mockk.every
import io.mockk.mockk
import kotlinx.coroutines.flow.flowOf
import kotlinx.coroutines.test.runTest
import org.junit.Assert.assertEquals
import org.junit.Before
import org.junit.Test

class LoginUseCaseTest {

    private lateinit var repository: AuthRepository
    private lateinit var useCase: LoginUseCase

    @Before
    fun setUp() {
        repository = mockk()
        useCase = LoginUseCase(repository)
    }

    @Test
    fun `invoke should call repository login and return its flow`() = runTest {
        // Arrange
        val loginResponse: LoginResponse = mockk()
        val expectedFlow = flowOf(ResultState.Loading, ResultState.Success(loginResponse))
        val request = mapOf("email" to "test@mail.com", "password" to "123456")
        
        every { repository.login(request) } returns expectedFlow

        // Act & Assert
        useCase(request).test {
            assertEquals(ResultState.Loading, awaitItem())
            val success = awaitItem() as ResultState.Success
            assertEquals(loginResponse, success.data)
            awaitComplete()
        }
    }
}
