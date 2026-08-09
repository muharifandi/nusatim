package com.nusatim.partner.features.profile.ui.support

import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.core.model.dto.SupportTicketResponse
import com.nusatim.partner.features.profile.domain.usecase.CreateSupportTicketUseCase
import com.nusatim.partner.features.profile.domain.usecase.GetSupportTicketDetailUseCase
import com.nusatim.partner.features.profile.domain.usecase.GetSupportTicketsUseCase
import com.nusatim.partner.features.profile.ui.support.state.SupportTicketsIntent
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
class SupportTicketsViewModelTest {

    private val getSupportTicketsUseCase: GetSupportTicketsUseCase = mockk()
    private val getSupportTicketDetailUseCase: GetSupportTicketDetailUseCase = mockk()
    private val createSupportTicketUseCase: CreateSupportTicketUseCase = mockk()
    private lateinit var viewModel: SupportTicketsViewModel
    private val testDispatcher = UnconfinedTestDispatcher()

    @Before
    fun setUp() {
        Dispatchers.setMain(testDispatcher)
        
        // Mock init call
        coEvery { getSupportTicketsUseCase(any(), any()) } returns flowOf(ResultState.Loading)
        
        viewModel = SupportTicketsViewModel(
            getSupportTicketsUseCase,
            getSupportTicketDetailUseCase,
            createSupportTicketUseCase
        )
    }

    @After
    fun tearDown() {
        Dispatchers.resetMain()
    }

    @Test
    fun `when load tickets successful, state should have data`() = runTest {
        val response = mockk<PagedBaseResponse<SupportTicketResponse>>()
        coEvery { getSupportTicketsUseCase(any(), any()) } returns flowOf(
            ResultState.Loading,
            ResultState.Success(response)
        )

        viewModel.processIntent(SupportTicketsIntent.LoadTickets())

        assertEquals(response, viewModel.state.value.ticketsResponse)
        assertEquals(false, viewModel.state.value.isLoading)
    }

    @Test
    fun `when create ticket successful, should refresh tickets`() = runTest {
        val ticket = mockk<SupportTicketResponse>()
        coEvery { createSupportTicketUseCase(any(), any()) } returns flowOf(
            ResultState.Loading,
            ResultState.Success(ticket)
        )
        
        coEvery { getSupportTicketsUseCase(any(), any()) } returns flowOf(ResultState.Success(mockk()))

        viewModel.processIntent(SupportTicketsIntent.CreateTicket("Subject", "Desc"))

        coEvery { getSupportTicketsUseCase(any(), any()) }
    }
}
