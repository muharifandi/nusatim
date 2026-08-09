package com.nusatim.partner.features.register.domain.usecase

import app.cash.turbine.test
import com.nusatim.partner.core.domain.repository.AuthRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.PartnerResponse
import io.mockk.every
import io.mockk.mockk
import kotlinx.coroutines.flow.flowOf
import kotlinx.coroutines.test.runTest
import org.junit.Assert.assertEquals
import org.junit.Before
import org.junit.Test

class RegisterUseCaseTest {

    private lateinit var repository: AuthRepository
    private lateinit var useCase: RegisterUseCase

    @Before
    fun setUp() {
        repository = mockk()
        useCase = RegisterUseCase(repository)
    }

    @Test
    fun `invoke should call repository register and return its flow`() = runTest {
        // Arrange
        val partnerResponse: PartnerResponse = mockk()
        val expectedFlow = flowOf(ResultState.Loading, ResultState.Success(partnerResponse))
        
        every {
            repository.register(any(), any(), any(), any(), any(), any(), any(), any(), any(), any(), any())
        } returns expectedFlow

        // Act & Assert
        useCase(
            mockk(), mockk(), mockk(), mockk(),
            mockk(), mockk(), null,
            mockk(), mockk(), mockk(),
            mockk()
        ).test {
            assertEquals(ResultState.Loading, awaitItem())
            val success = awaitItem() as ResultState.Success
            assertEquals(partnerResponse, success.data)
            awaitComplete()
        }
    }
}
