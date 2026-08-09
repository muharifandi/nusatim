package com.nusatim.partner.features.home.domain.usecase

import app.cash.turbine.test
import com.nusatim.partner.core.domain.repository.AuthRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.DashboardResponse
import io.mockk.every
import io.mockk.mockk
import kotlinx.coroutines.flow.flowOf
import kotlinx.coroutines.test.runTest
import org.junit.Assert.assertEquals
import org.junit.Before
import org.junit.Test

class GetDashboardUseCaseTest {

    private lateinit var repository: AuthRepository
    private lateinit var useCase: GetDashboardUseCase

    @Before
    fun setUp() {
        repository = mockk()
        useCase = GetDashboardUseCase(repository)
    }

    @Test
    fun `invoke should call repository getDashboard and return its flow`() = runTest {
        // Arrange
        val dashboardResponse: DashboardResponse = mockk()
        val expectedFlow = flowOf(ResultState.Loading, ResultState.Success(dashboardResponse))
        
        every { repository.getDashboard() } returns expectedFlow

        // Act & Assert
        useCase().test {
            assertEquals(ResultState.Loading, awaitItem())
            val success = awaitItem() as ResultState.Success
            assertEquals(dashboardResponse, success.data)
            awaitComplete()
        }
    }
}
