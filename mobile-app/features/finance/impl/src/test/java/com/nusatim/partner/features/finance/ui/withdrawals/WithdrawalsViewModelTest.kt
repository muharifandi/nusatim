package com.nusatim.partner.features.finance.ui.withdrawals

import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.core.model.dto.WithdrawalBalanceResponse
import com.nusatim.partner.core.model.dto.WithdrawalResponse
import com.nusatim.partner.features.finance.domain.usecase.*
import com.nusatim.partner.features.profile.domain.usecase.GetProfileUseCase
import com.nusatim.partner.features.finance.ui.withdrawals.state.WithdrawalsIntent
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
import java.io.File

@OptIn(ExperimentalCoroutinesApi::class)
class WithdrawalsViewModelTest {

    private val getWithdrawalsUseCase: GetWithdrawalsUseCase = mockk()
    private val getWithdrawalBalanceUseCase: GetWithdrawalBalanceUseCase = mockk()
    private val getWithdrawalDetailUseCase: GetWithdrawalDetailUseCase = mockk()
    private val requestWithdrawalUseCase: RequestWithdrawalUseCase = mockk()
    private val getProfileUseCase: GetProfileUseCase = mockk()

    private lateinit var viewModel: WithdrawalsViewModel
    private val testDispatcher = UnconfinedTestDispatcher()

    @Before
    fun setUp() {
        Dispatchers.setMain(testDispatcher)

        // Mock init calls
        coEvery { getWithdrawalsUseCase(any(), any()) } returns flowOf(ResultState.Loading)
        coEvery { getWithdrawalBalanceUseCase() } returns flowOf(ResultState.Loading)
        coEvery { getProfileUseCase() } returns flowOf(ResultState.Loading)

        viewModel = WithdrawalsViewModel(
            getWithdrawalsUseCase,
            getWithdrawalBalanceUseCase,
            getWithdrawalDetailUseCase,
            requestWithdrawalUseCase,
            getProfileUseCase
        )
    }

    @After
    fun tearDown() {
        Dispatchers.resetMain()
    }

    @Test
    fun `when load withdrawals successful, state should have data`() = runTest {
        val response = mockk<PagedBaseResponse<WithdrawalResponse>>()
        coEvery { getWithdrawalsUseCase(any(), any()) } returns flowOf(
            ResultState.Loading,
            ResultState.Success(response)
        )

        viewModel.processIntent(WithdrawalsIntent.LoadWithdrawals(1))

        assertEquals(response, viewModel.state.value.withdrawalsResponse)
        assertEquals(false, viewModel.state.value.isLoading)
    }

    @Test
    fun `when load balance successful, state should have balance`() = runTest {
        val balance = WithdrawalBalanceResponse(2000000, 100000)
        coEvery { getWithdrawalBalanceUseCase() } returns flowOf(
            ResultState.Loading,
            ResultState.Success(balance)
        )

        viewModel.processIntent(WithdrawalsIntent.LoadBalance)

        assertEquals(balance, viewModel.state.value.balance)
    }

    @Test
    fun `when submit request successful, should refresh data`() = runTest {
        val file = mockk<File> { coEvery { name } returns "ktp.jpg" }
        coEvery { requestWithdrawalUseCase(any(), any(), any()) } returns flowOf(
            ResultState.Loading,
            ResultState.Success(mockk())
        )

        // Mock refresh calls
        coEvery { getWithdrawalsUseCase(any(), any()) } returns flowOf(ResultState.Success(mockk()))
        coEvery { getWithdrawalBalanceUseCase() } returns flowOf(ResultState.Success(mockk()))

        viewModel.processIntent(WithdrawalsIntent.SubmitRequest(500000, file, "Note"))

        coEvery { getWithdrawalsUseCase(any(), any()) }
        coEvery { getWithdrawalBalanceUseCase() }
    }
}
