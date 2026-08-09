package com.nusatim.partner.features.finance.ui.commissions

import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.CommissionResponse
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.features.finance.domain.usecase.GetCommissionDetailUseCase
import com.nusatim.partner.features.finance.domain.usecase.GetCommissionsUseCase
import com.nusatim.partner.features.finance.ui.commissions.state.CommissionsIntent
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
class CommissionsViewModelTest {

    private val getCommissionsUseCase: GetCommissionsUseCase = mockk()
    private val getCommissionDetailUseCase: GetCommissionDetailUseCase = mockk()
    private lateinit var viewModel: CommissionsViewModel
    private val testDispatcher = UnconfinedTestDispatcher()

    @Before
    fun setUp() {
        Dispatchers.setMain(testDispatcher)
        
        // Mock init call
        coEvery { getCommissionsUseCase(any(), any(), any()) } returns flowOf(ResultState.Loading)
        
        viewModel = CommissionsViewModel(getCommissionsUseCase, getCommissionDetailUseCase)
    }

    @After
    fun tearDown() {
        Dispatchers.resetMain()
    }

    @Test
    fun `when load commissions successful, state should have data`() = runTest {
        val response = mockk<PagedBaseResponse<CommissionResponse>>()
        coEvery { getCommissionsUseCase(any(), any(), any()) } returns flowOf(
            ResultState.Loading,
            ResultState.Success(response)
        )

        viewModel.processIntent(CommissionsIntent.LoadCommissions())

        assertEquals(response, viewModel.state.value.commissionsResponse)
        assertEquals(false, viewModel.state.value.isLoading)
    }

    @Test
    fun `when load commissions with status filter, usecase should be called with status`() = runTest {
        coEvery { getCommissionsUseCase("approved", any(), any()) } returns flowOf(
            ResultState.Success(mockk())
        )

        viewModel.processIntent(CommissionsIntent.LoadCommissions(status = "approved"))

        coEvery { getCommissionsUseCase("approved", any(), any()) }
        assertEquals("approved", viewModel.state.value.statusFilter)
    }

    @Test
    fun `when load detail successful, state should have selected commission`() = runTest {
        val commission = mockk<CommissionResponse>()
        coEvery { getCommissionDetailUseCase(1) } returns flowOf(
            ResultState.Loading,
            ResultState.Success(commission)
        )

        viewModel.processIntent(CommissionsIntent.LoadCommissionDetail(1))

        assertEquals(commission, viewModel.state.value.selectedCommission)
        assertEquals(false, viewModel.state.value.isLoading)
    }
}
