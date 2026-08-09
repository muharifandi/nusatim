package com.nusatim.partner.features.profile.domain.usecase

import app.cash.turbine.test
import com.nusatim.partner.core.domain.repository.ProfileRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.PartnerResponse
import io.mockk.every
import io.mockk.mockk
import kotlinx.coroutines.flow.flowOf
import kotlinx.coroutines.test.runTest
import org.junit.Assert.assertEquals
import org.junit.Before
import org.junit.Test

class GetProfileUseCaseTest {

    private lateinit var repository: ProfileRepository
    private lateinit var useCase: GetProfileUseCase

    @Before
    fun setUp() {
        repository = mockk()
        useCase = GetProfileUseCase(repository)
    }

    @Test
    fun `invoke should call repository getProfile and return its flow`() = runTest {
        // Arrange
        val partnerResponse: PartnerResponse = mockk()
        val expectedFlow = flowOf(ResultState.Loading, ResultState.Success(partnerResponse))
        
        every { repository.getProfile() } returns expectedFlow

        // Act & Assert
        useCase().test {
            assertEquals(ResultState.Loading, awaitItem())
            val success = awaitItem() as ResultState.Success
            assertEquals(partnerResponse, success.data)
            awaitComplete()
        }
    }
}
