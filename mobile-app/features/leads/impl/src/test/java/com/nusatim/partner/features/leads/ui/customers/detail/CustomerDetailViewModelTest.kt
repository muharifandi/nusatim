package com.nusatim.partner.features.leads.ui.customers.detail

import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.CustomerResponse
import com.nusatim.partner.features.leads.domain.usecase.GetCustomerDetailUseCase
import com.nusatim.partner.features.leads.domain.usecase.UpdateCustomerProgressUseCase
import com.nusatim.partner.features.leads.domain.usecase.UpdateCustomerUseCase
import com.nusatim.partner.features.leads.ui.customers.detail.state.CustomerDetailIntent
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
class CustomerDetailViewModelTest {

    private val getCustomerDetailUseCase: GetCustomerDetailUseCase = mockk()
    private val updateCustomerUseCase: UpdateCustomerUseCase = mockk()
    private val updateCustomerProgressUseCase: UpdateCustomerProgressUseCase = mockk()

    private lateinit var viewModel: CustomerDetailViewModel
    private val testDispatcher = UnconfinedTestDispatcher()

    @Before
    fun setUp() {
        Dispatchers.setMain(testDispatcher)
        viewModel = CustomerDetailViewModel(
            getCustomerDetailUseCase,
            updateCustomerUseCase,
            updateCustomerProgressUseCase
        )
    }

    @After
    fun tearDown() {
        Dispatchers.resetMain()
    }

    @Test
    fun `when load customer successful, state should have data`() = runTest {
        val customer = mockk<CustomerResponse>()
        coEvery { getCustomerDetailUseCase(1) } returns flowOf(
            ResultState.Loading,
            ResultState.Success(customer)
        )

        viewModel.processIntent(CustomerDetailIntent.LoadCustomer(1))

        assertEquals(customer, viewModel.state.value.customer)
        assertEquals(false, viewModel.state.value.isLoading)
    }

    @Test
    fun `when update progress successful, state should be updated`() = runTest {
        val customer = mockk<CustomerResponse>()
        coEvery { updateCustomerProgressUseCase(1, 75) } returns flowOf(
            ResultState.Loading,
            ResultState.Success(customer)
        )

        viewModel.processIntent(CustomerDetailIntent.UpdateProgress(1, 75))

        assertEquals(customer, viewModel.state.value.customer)
        assertEquals(false, viewModel.state.value.isLoading)
    }
}
