package com.nusatim.partner.features.leads.ui.customers

import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.CustomerResponse
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.features.leads.domain.usecase.GetCustomersUseCase
import com.nusatim.partner.features.leads.ui.customers.state.CustomersIntent
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
class CustomersViewModelTest {

    private val getCustomersUseCase: GetCustomersUseCase = mockk()
    private lateinit var viewModel: CustomersViewModel
    private val testDispatcher = UnconfinedTestDispatcher()

    @Before
    fun setUp() {
        Dispatchers.setMain(testDispatcher)
        
        coEvery { getCustomersUseCase(any(), any()) } returns flowOf(ResultState.Loading)
        
        viewModel = CustomersViewModel(getCustomersUseCase)
    }

    @After
    fun tearDown() {
        Dispatchers.resetMain()
    }

    @Test
    fun `when load customers successful, state should have data`() = runTest {
        val response = mockk<PagedBaseResponse<CustomerResponse>>()
        coEvery { getCustomersUseCase(any(), any()) } returns flowOf(
            ResultState.Loading,
            ResultState.Success(response)
        )

        viewModel.processIntent(CustomersIntent.LoadCustomers(1))

        assertEquals(response, viewModel.state.value.customersResponse)
        assertEquals(false, viewModel.state.value.isLoading)
    }
}
