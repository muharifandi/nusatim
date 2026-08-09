package com.nusatim.partner.features.leads.ui.leads

import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.LeadResponse
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.features.leads.domain.usecase.GetLeadsUseCase
import com.nusatim.partner.features.leads.ui.leads.state.LeadsIntent
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
class LeadsViewModelTest {

    private lateinit var getLeadsUseCase: GetLeadsUseCase
    private lateinit var viewModel: LeadsViewModel
    private val testDispatcher = UnconfinedTestDispatcher()

    @Before
    fun setUp() {
        Dispatchers.setMain(testDispatcher)
        getLeadsUseCase = mockk()
        
        // Mock init call
        coEvery { getLeadsUseCase(any(), any(), any(), any(), any()) } returns flowOf(ResultState.Loading)
        
        viewModel = LeadsViewModel(getLeadsUseCase)
    }

    @After
    fun tearDown() {
        Dispatchers.resetMain()
    }

    @Test
    fun `when load leads successful, state should have data`() = runTest {
        // Arrange
        val response = mockk<PagedBaseResponse<LeadResponse>>()
        coEvery { getLeadsUseCase(any(), any(), any(), any(), any()) } returns flowOf(
            ResultState.Loading,
            ResultState.Success(response)
        )

        // Act
        viewModel.processIntent(LeadsIntent.LoadLeads())

        // Assert
        assertEquals(response, viewModel.state.value.leadsResponse)
        assertEquals(false, viewModel.state.value.isLoading)
    }

    @Test
    fun `when load leads fails, state should show error`() = runTest {
        coEvery { getLeadsUseCase(any(), any(), any(), any(), any()) } returns flowOf(
            ResultState.Loading,
            ResultState.Error("Network error")
        )

        viewModel.processIntent(LeadsIntent.LoadLeads())

        assertEquals("Network error", viewModel.state.value.error)
        assertEquals(false, viewModel.state.value.isLoading)
    }
}
