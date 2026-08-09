package com.nusatim.partner.features.leads.ui.leads.detail

import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.*
import com.nusatim.partner.features.leads.domain.usecase.*
import com.nusatim.partner.features.leads.ui.leads.detail.state.LeadDetailEffect
import com.nusatim.partner.features.leads.ui.leads.detail.state.LeadDetailIntent
import io.mockk.coEvery
import io.mockk.every
import io.mockk.mockk
import app.cash.turbine.test
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
class LeadDetailViewModelTest {

    private val getLeadDetailUseCase: GetLeadDetailUseCase = mockk()
    private val createLeadUseCase: CreateLeadUseCase = mockk()
    private val updateLeadUseCase: UpdateLeadUseCase = mockk()
    private val updateLeadStatusUseCase: UpdateLeadStatusUseCase = mockk()
    private val getLeadRemindersUseCase: GetLeadRemindersUseCase = mockk()
    private val completeReminderUseCase: CompleteReminderUseCase = mockk()
    private val getLeadActivitiesUseCase: GetLeadActivitiesUseCase = mockk()
    private val createLeadNoteUseCase: CreateLeadNoteUseCase = mockk()
    private val getLeadDocumentsUseCase: GetLeadDocumentsUseCase = mockk()
    private val uploadLeadDocumentUseCase: UploadLeadDocumentUseCase = mockk()
    private val deleteLeadUseCase: DeleteLeadUseCase = mockk()
    private val getCustomerByLeadIdUseCase: GetCustomerByLeadIdUseCase = mockk()

    private lateinit var viewModel: LeadDetailViewModel
    private val testDispatcher = UnconfinedTestDispatcher()

    @Before
    fun setUp() {
        Dispatchers.setMain(testDispatcher)
        viewModel = LeadDetailViewModel(
            getLeadDetailUseCase,
            createLeadUseCase,
            updateLeadUseCase,
            updateLeadStatusUseCase,
            getLeadRemindersUseCase,
            completeReminderUseCase,
            getLeadActivitiesUseCase,
            createLeadNoteUseCase,
            getLeadDocumentsUseCase,
            uploadLeadDocumentUseCase,
            deleteLeadUseCase,
            getCustomerByLeadIdUseCase
        )
    }

    @After
    fun tearDown() {
        Dispatchers.resetMain()
    }

    @Test
    fun `when load lead successful, state should have data`() = runTest {
        val lead = mockk<LeadResponse>()
        coEvery { getLeadDetailUseCase(1) } returns flowOf(
            ResultState.Loading,
            ResultState.Success(lead)
        )

        viewModel.processIntent(LeadDetailIntent.LoadLead(1))

        assertEquals(lead, viewModel.state.value.lead)
        assertEquals(false, viewModel.state.value.isLoading)
    }

    @Test
    fun `when update status successful, state should be updated`() = runTest {
        val lead = mockk<LeadResponse>()
        coEvery { updateLeadStatusUseCase(1, "won") } returns flowOf(
            ResultState.Loading,
            ResultState.Success(lead)
        )
        // Mock refresh activities
        coEvery { getLeadActivitiesUseCase(1) } returns flowOf(ResultState.Success(emptyList()))

        viewModel.processIntent(LeadDetailIntent.UpdateStatus(1, "won"))

        assertEquals(lead, viewModel.state.value.lead)
        assertEquals(false, viewModel.state.value.isLoading)
        coEvery { getLeadActivitiesUseCase(1) }
    }

    @Test
    fun `when load reminders successful, state should have reminders`() = runTest {
        val reminders = listOf(mockk<LeadReminderResponse>())
        coEvery { getLeadRemindersUseCase(1) } returns flowOf(
            ResultState.Loading,
            ResultState.Success(reminders)
        )

        viewModel.processIntent(LeadDetailIntent.LoadReminders(1))

        assertEquals(reminders, viewModel.state.value.reminders)
        assertEquals(false, viewModel.state.value.isLoading)
    }

    @Test
    fun `when load activities successful, state should have activities`() = runTest {
        val activities = listOf(mockk<LeadActivityResponse>())
        coEvery { getLeadActivitiesUseCase(1) } returns flowOf(
            ResultState.Loading,
            ResultState.Success(activities)
        )

        viewModel.processIntent(LeadDetailIntent.LoadActivities(1))

        assertEquals(activities, viewModel.state.value.activities)
        assertEquals(false, viewModel.state.value.isLoading)
    }

    @Test
    fun `when upload document successful, should refresh documents and activities`() = runTest {
        val file = mockk<File> { coEvery { name } returns "test.jpg" }
        coEvery { uploadLeadDocumentUseCase(any(), any(), any()) } returns flowOf(
            ResultState.Loading,
            ResultState.Success(mockk())
        )
        coEvery { getLeadDocumentsUseCase(1) } returns flowOf(ResultState.Success(emptyList()))
        coEvery { getLeadActivitiesUseCase(1) } returns flowOf(ResultState.Success(emptyList()))

        viewModel.processIntent(LeadDetailIntent.UploadDocument(1, file, "KTP"))

        coEvery { getLeadDocumentsUseCase(1) }
        coEvery { getLeadActivitiesUseCase(1) }
    }

    @Test
    fun `when navigate to customer successful, should emit navigate effect`() = runTest {
        val customer = mockk<CustomerResponse> { every { id } returns 201 }
        coEvery { getCustomerByLeadIdUseCase(1) } returns flowOf(
            ResultState.Loading,
            ResultState.Success(customer)
        )

        viewModel.effect.test {
            viewModel.processIntent(LeadDetailIntent.NavigateToCustomer(1))
            val effect = awaitItem()
            assert(effect is LeadDetailEffect.NavigateToCustomerDetail && effect.customerId == 201)
        }
    }
}
