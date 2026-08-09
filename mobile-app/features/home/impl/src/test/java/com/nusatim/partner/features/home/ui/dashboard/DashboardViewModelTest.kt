package com.nusatim.partner.features.home.ui.dashboard

import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.DashboardResponse
import com.nusatim.partner.features.home.domain.usecase.GetDashboardUseCase
import com.nusatim.partner.features.home.ui.dashboard.state.DashboardIntent
import io.mockk.coEvery
import io.mockk.mockk
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.ExperimentalCoroutinesApi
import kotlinx.coroutines.flow.flowOf
import kotlinx.coroutines.test.UnconfinedTestDispatcher
import kotlinx.coroutines.test.resetMain
import kotlinx.coroutines.test.runTest
import kotlinx.coroutines.test.setMain
import org.junit.After
import org.junit.Assert.assertEquals
import org.junit.Before
import org.junit.Test

@OptIn(ExperimentalCoroutinesApi::class)
class DashboardViewModelTest {

    private lateinit var getDashboardUseCase: GetDashboardUseCase
    private lateinit var viewModel: DashboardViewModel
    private val testDispatcher = UnconfinedTestDispatcher()

    @Before
    fun setUp() {
        Dispatchers.setMain(testDispatcher)
        getDashboardUseCase = mockk()
        
        // Mock init call
        coEvery { getDashboardUseCase() } returns flowOf(ResultState.Loading)
        
        viewModel = DashboardViewModel(getDashboardUseCase)
    }

    @After
    fun tearDown() {
        Dispatchers.resetMain()
    }

    @Test
    fun `when load dashboard successful, state should have dashboard data`() = runTest {
        // Arrange
        val dashboardResponse = mockk<DashboardResponse>()
        coEvery { getDashboardUseCase() } returns flowOf(
            ResultState.Loading,
            ResultState.Success(dashboardResponse)
        )

        // Act
        viewModel.processIntent(DashboardIntent.LoadDashboard)

        // Assert
        assertEquals(dashboardResponse, viewModel.state.value.dashboard)
        assertEquals(false, viewModel.state.value.isLoading)
    }

    @Test
    fun `when load dashboard fails, state should show error`() = runTest {
        // Arrange
        coEvery { getDashboardUseCase() } returns flowOf(
            ResultState.Loading,
            ResultState.Error("Failed to load dashboard")
        )

        // Act
        viewModel.processIntent(DashboardIntent.LoadDashboard)

        // Assert
        assertEquals("Failed to load dashboard", viewModel.state.value.error)
        assertEquals(false, viewModel.state.value.isLoading)
    }
}
