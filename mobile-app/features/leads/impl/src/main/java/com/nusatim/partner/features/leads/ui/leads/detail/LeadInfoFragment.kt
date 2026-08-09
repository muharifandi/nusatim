package com.nusatim.partner.features.leads.ui.leads.detail

import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.features.leads.databinding.FragmentLeadInfoBinding
import com.nusatim.partner.features.leads.ui.leads.detail.state.LeadDetailIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch
import java.text.NumberFormat
import java.util.*

@AndroidEntryPoint
class LeadInfoFragment : BaseFragment<FragmentLeadInfoBinding>() {

    private val viewModel: LeadDetailViewModel by viewModels({ requireParentFragment() })

    override fun onInitViews() {
        binding.btnChangeStatus.setOnClickListener {
            val currentStatus = viewModel.state.value.lead?.status ?: "new"
            val leadId = viewModel.state.value.lead?.id ?: return@setOnClickListener
            
            ChangeStatusBottomSheet(currentStatus) { newStatus ->
                viewModel.processIntent(LeadDetailIntent.UpdateStatus(leadId, newStatus))
            }.show(childFragmentManager, ChangeStatusBottomSheet.TAG)
        }

        binding.btnViewCustomer.setOnClickListener {
            val leadId = viewModel.state.value.lead?.id ?: return@setOnClickListener
            viewModel.processIntent(LeadDetailIntent.NavigateToCustomer(leadId))
        }
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                viewModel.state.collect { state ->
                    state.lead?.let { lead ->
                        binding.tvName.text = lead.name ?: "-"
                        binding.tvPhone.text = lead.phone ?: "-"
                        binding.tvEmail.text = lead.email ?: "-"
                        binding.tvProduct.text = lead.serviceName ?: "-"
                        binding.tvEstimation.text = formatRupiah(lead.estimatedValue ?: 0)
                        binding.btnStatus.text = lead.status?.replaceFirstChar { it.uppercase() } ?: "-"
                        
                        val colorRes = when (lead.status?.lowercase()) {
                            "new", "pending", "open", "draft" -> com.nusatim.partner.core.ui.R.color.status_neutral
                            "in_progress", "waiting_payment", "opportunity" -> com.nusatim.partner.core.ui.R.color.status_warning
                            "assigned", "contacted", "qualified", "proposal", "negotiation" -> com.nusatim.partner.core.ui.R.color.status_info
                            "won", "paid", "closed", "resolved" -> com.nusatim.partner.core.ui.R.color.status_success
                            "lost", "rejected", "cancelled", "suspended" -> com.nusatim.partner.core.ui.R.color.partner_error
                            else -> com.nusatim.partner.core.ui.R.color.status_neutral
                        }
                        binding.btnStatus.setBackgroundTintList(android.content.res.ColorStateList.valueOf(
                            androidx.core.content.ContextCompat.getColor(requireContext(), colorRes)
                        ))
                        
                        binding.btnViewCustomer.visibility = if (lead.status == "won") android.view.View.VISIBLE else android.view.View.GONE
                    }
                }
            }
        }
    }

    private fun formatRupiah(amount: Long): String {
        val format = NumberFormat.getCurrencyInstance(Locale("id", "ID"))
        return format.format(amount).replace(",00", "")
    }
}
