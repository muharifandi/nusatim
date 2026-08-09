package com.nusatim.partner.features.finance.ui.commissions.detail

import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.fragment.findNavController
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.core.model.dto.CommissionResponse
import com.nusatim.partner.features.finance.databinding.FragmentCommissionDetailBinding
import com.nusatim.partner.features.finance.ui.commissions.CommissionsViewModel
import com.nusatim.partner.features.finance.ui.commissions.state.CommissionsIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch
import java.text.NumberFormat
import java.util.*

@AndroidEntryPoint
class CommissionDetailFragment : BaseFragment<FragmentCommissionDetailBinding>() {

    private val viewModel: CommissionsViewModel by viewModels()
    private val commissionId by lazy { arguments?.getInt("id") ?: -1 }

    override fun onInitViews() {
        binding.layoutToolbar.toolbar.setNavigationOnClickListener {
            findNavController().navigateUp()
        }
        
        binding.layoutToolbar.toolbar.title = "Detail Komisi"

        if (commissionId != -1) {
            viewModel.processIntent(CommissionsIntent.LoadCommissionDetail(commissionId))
        }
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                viewModel.state.collect { state ->
                    state.selectedCommission?.let { renderDetail(it) }
                }
            }
        }
    }

    private fun renderDetail(item: CommissionResponse) {
        binding.tvAmount.text = formatRupiah(item.amount)
        binding.tvDate.text = getString(com.nusatim.partner.features.finance.R.string.commission_label_created_at, item.createdAt)
        binding.tvCustomerName.text = item.customerName ?: "Bonus"
        binding.tvServiceName.text = item.serviceName ?: "-"
        binding.tvProjectValue.text = formatRupiah(item.projectValue)
        binding.tvPercentage.text = getString(com.nusatim.partner.features.finance.R.string.commission_label_percent, item.percentage.toInt())
        binding.tvType.text = item.type.replaceFirstChar { it.uppercase() }

        binding.btnStatus.text = item.status?.replaceFirstChar { it.uppercase() } ?: "-"
        val statusColor = when (item.status?.lowercase()) {
            "pending", "waiting_client_payment" -> com.nusatim.partner.core.ui.R.color.status_warning
            "approved" -> com.nusatim.partner.core.ui.R.color.status_info
            "paid" -> com.nusatim.partner.core.ui.R.color.status_success
            "rejected" -> com.nusatim.partner.core.ui.R.color.partner_error
            else -> com.nusatim.partner.core.ui.R.color.status_neutral
        }
        binding.btnStatus.setBackgroundTintList(android.content.res.ColorStateList.valueOf(
            androidx.core.content.ContextCompat.getColor(requireContext(), statusColor)
        ))

        if (item.status == "rejected" && !item.rejectionReason.isNullOrEmpty()) {
            binding.cardRejection.visibility = android.view.View.VISIBLE
            binding.tvRejectionReason.text = item.rejectionReason
        } else {
            binding.cardRejection.visibility = android.view.View.GONE
        }
    }

    private fun formatRupiah(amount: Long): String {
        val format = NumberFormat.getCurrencyInstance(Locale("id", "ID"))
        return format.format(amount).replace(",00", "")
    }
}
