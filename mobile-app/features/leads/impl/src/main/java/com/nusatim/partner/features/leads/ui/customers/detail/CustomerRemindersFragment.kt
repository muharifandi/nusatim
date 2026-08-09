package com.nusatim.partner.features.leads.ui.customers.detail

import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.recyclerview.widget.LinearLayoutManager
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.core.model.dto.LeadReminderResponse
import com.nusatim.partner.features.leads.databinding.FragmentLeadReminderBinding
import com.nusatim.partner.features.leads.ui.leads.detail.LeadReminderAdapter
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch

@AndroidEntryPoint
class CustomerRemindersFragment : BaseFragment<FragmentLeadReminderBinding>() {

    private val viewModel: CustomerDetailViewModel by viewModels({ requireParentFragment() })
    private val adapter by lazy {
        LeadReminderAdapter { _ ->
            // Read-only
        }
    }

    override fun onInitViews() {
        binding.rvReminders.layoutManager = LinearLayoutManager(requireContext())
        binding.rvReminders.adapter = adapter
        binding.fabAddReminder.visibility = android.view.View.GONE // Read-only
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                viewModel.state.collect { state ->
                    val customer = state.customer
                    if (customer != null) {
                        val reminders = mutableListOf<LeadReminderResponse>()
                        customer.followUps?.map { 
                            LeadReminderResponse(it.id, customer.id, "follow_up", it.remindAt, it.note, it.completedAt) 
                        }?.let { reminders.addAll(it) }
                        
                        customer.meetings?.map { 
                            LeadReminderResponse(it.id, customer.id, "meeting", it.remindAt, it.note, it.completedAt) 
                        }?.let { reminders.addAll(it) }
                        
                        val sortedReminders = reminders.sortedByDescending { it.remindAt }
                        adapter.submitList(sortedReminders)
                        
                        val isEmpty = sortedReminders.isEmpty()
                        binding.layoutEmpty.root.visibility = if (isEmpty) android.view.View.VISIBLE else android.view.View.GONE
                        if (isEmpty) {
                            binding.layoutEmpty.tvErrorMessage.text = "Belum ada riwayat follow up atau meeting"
                            binding.layoutEmpty.btnRetry.visibility = android.view.View.GONE
                        }
                    }
                }
            }
        }
    }
}
