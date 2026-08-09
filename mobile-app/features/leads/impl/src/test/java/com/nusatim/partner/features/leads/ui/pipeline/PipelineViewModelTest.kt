package com.nusatim.partner.features.leads.ui.pipeline

import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.PipelineResponse
import com.nusatim.partner.features.leads.domain.usecase.GetPipelineUseCase
import com.nusatim.partner.features.leads.domain.usecase.UpdateLeadStatusUseCase
import com.nusatim.partner.features.leads.ui.pipeline.state.PipelineIntent
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
class PipelineViewModelTest {

    private val getPipelineUseCase: GetPipelineUseCase = mockk()
    private val updateLeadStatusUseCase: UpdateLeadStatusUseCase = mockk()
    private lateinit var viewModel: PipelineViewModel
    private val testDispatcher = UnconfinedTestDispatcher()

    @Before
    fun setUp() {
        Dispatchers.setMain(testDispatcher)
        
        // Mock init call
        coEvery { getPipelineUseCase(any(), any(), any()) } returns flowOf(ResultState.Loading)
        
        viewModel = PipelineViewModel(getPipelineUseCase, updateLeadStatusUseCase)
    }

    @After
    fun tearDown() {
        Dispatchers.resetMain()
    }

    @Test
    fun `when load pipeline successful, state should have data`() = runTest {
        val response = mockk<PipelineResponse>()
        coEvery { getPipelineUseCase(any(), any(), any()) } returns flowOf(
            ResultState.Loading,
            ResultState.Success(response)
        )

        viewModel.processIntent(PipelineIntent.LoadPipeline)

        assertEquals(response, viewModel.state.value.pipeline)
        assertEquals(false, viewModel.state.value.isLoading)
    }

    @Test
    fun `when update status successful, pipeline should be refreshed`() = runTest {
        coEvery { updateLeadStatusUseCase(1, "contacted") } returns flowOf(
            ResultState.Loading,
            ResultState.Success(mockk())
        )
        val response = mockk<PipelineResponse>()
        coEvery { getPipelineUseCase(any(), any(), any()) } returns flowOf(ResultState.Success(response))

        viewModel.processIntent(PipelineIntent.UpdateStatus(1, "contacted"))

        coEvery { getPipelineUseCase(any(), any(), any()) }
        assertEquals(response, viewModel.state.value.pipeline)
    }
}
